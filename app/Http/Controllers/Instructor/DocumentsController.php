<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    /**
     * List current documents (latest verified per type/side) and submissions history.
     */
    public function index(Request $request): JsonResponse
    {
        $profile = Auth::user()?->instructorProfile;
        if (! $profile) {
            return response()->json(['data' => ['current' => [], 'submissions' => []]]);
        }

        $docs = $profile->documents()->orderBy('created_at', 'desc')->get();

        $current = [
            'drivers_licence' => ['front' => null, 'back' => null, 'expires_at' => null],
            'instructor_licence' => ['front' => null, 'back' => null, 'expires_at' => null],
            'wwcc' => null,
        ];

        foreach (['drivers_licence', 'instructor_licence'] as $type) {
            $byType = $docs->where('type', $type)->sortByDesc('created_at');
            foreach (['front', 'back'] as $side) {
                $v = $byType->where('side', $side)->first();
                if ($v) {
                    $current[$type][$side] = ['expires_at' => $v->expires_at?->format('j M Y'), 'status' => $v->status];
                }
            }
            $any = $byType->first();
            if ($any) {
                $current[$type]['expires_at'] = $any->expires_at?->format('j M Y');
            }
        }

        $wwccDoc = $docs->where('type', 'wwcc')->sortByDesc('created_at')->first();
        if ($wwccDoc) {
            $current['wwcc'] = ['expires_at' => $wwccDoc->expires_at?->format('j M Y'), 'status' => $wwccDoc->status];
        }

        $submissions = $docs->take(50)->map(fn ($d) => [
            'id' => $d->id,
            'submission_date' => $d->created_at->format('Y-m-d H:i'),
            'status' => $d->status,
            'document' => $this->documentLabel($d),
        ])->values();

        return response()->json([
            'data' => [
                'current' => $current,
                'submissions' => $submissions,
                'profile_wwcc' => [
                    'wwcc_number' => $profile->wwcc_number,
                    'wwcc_verified_at' => $profile->wwcc_verified_at?->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Submit a new document (file upload + expiry).
     */
    public function store(Request $request): JsonResponse
    {
        $profile = Auth::user()?->instructorProfile;
        if (! $profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $type = $request->input('type');
        $validTypes = ['drivers_licence', 'instructor_licence', 'wwcc'];
        if (! in_array($type, $validTypes, true)) {
            return response()->json(['message' => 'Invalid document type.'], 422);
        }

        $expiresAt = $request->input('expires_at');
        if ($expiresAt) {
            $expiresAt = \Carbon\Carbon::parse($expiresAt)->format('Y-m-d');
        }

        $rules = [
            'expires_at' => ['nullable', 'date'],
            'front_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'back_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
        $request->validate($rules);

        $dir = 'instructor-documents/'.$profile->id;

        // Sensitive documents — stored privately on DigitalOcean Spaces.
        // Use signed temporaryUrl() in admin views to grant time-limited access.
        $storeOpts = ['disk' => 'spaces', 'visibility' => 'private'];

        if (in_array($type, ['drivers_licence', 'instructor_licence'], true)) {
            if ($request->hasFile('front_file')) {
                $path = $request->file('front_file')->store($dir, $storeOpts);
                InstructorDocument::create([
                    'instructor_profile_id' => $profile->id,
                    'type' => $type,
                    'side' => 'front',
                    'file_path' => $path,
                    'expires_at' => $expiresAt,
                    'status' => InstructorDocument::STATUS_PENDING,
                ]);
            }
            if ($request->hasFile('back_file')) {
                $path = $request->file('back_file')->store($dir, $storeOpts);
                InstructorDocument::create([
                    'instructor_profile_id' => $profile->id,
                    'type' => $type,
                    'side' => 'back',
                    'file_path' => $path,
                    'expires_at' => $expiresAt,
                    'status' => InstructorDocument::STATUS_PENDING,
                ]);
            }
            if (! $request->hasFile('front_file') && ! $request->hasFile('back_file')) {
                return response()->json(['message' => 'Please upload at least one file.'], 422);
            }
        } else {
            // A document must have an actual file — otherwise a fileless (unverifiable)
            // WWCC row would be created and wrongly count toward "documents submitted".
            if (! $request->hasFile('file')) {
                return response()->json(['message' => 'Please upload the document file.'], 422);
            }
            $path = $request->file('file')->store($dir, $storeOpts);
            $profile->update(['wwcc_number' => $request->input('wwcc_number') ?: $profile->wwcc_number]);
            InstructorDocument::create([
                'instructor_profile_id' => $profile->id,
                'type' => 'wwcc',
                'side' => null,
                'file_path' => $path,
                'expires_at' => $expiresAt,
                'status' => InstructorDocument::STATUS_PENDING,
            ]);
        }

        // Auto-advance verification_status to 'documents_submitted' once all 3
        // required doc types are uploaded (still requires admin approval to become 'verified').
        $requiredTypes = ['drivers_licence', 'instructor_licence', 'wwcc'];
        $submittedTypes = InstructorDocument::where('instructor_profile_id', $profile->id)
            ->whereIn('type', $requiredTypes)
            ->where('status', '!=', InstructorDocument::STATUS_REJECTED)
            ->pluck('type')
            ->unique();

        if ($submittedTypes->count() === count($requiredTypes)
            && in_array($profile->verification_status, ['pending', 'rejected'], true)) {
            $profile->update(['verification_status' => 'documents_submitted']);

            // Alert all admins that documents are now ready for verification.
            try {
                foreach (\App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get() as $admin) {
                    $admin->notify(new \App\Notifications\InstructorDocumentsSubmittedAdmin($profile->user));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Instructor documents-submitted admin alert failed: ' . $e->getMessage());
            }
        }

        return response()->json(['data' => ['message' => 'Submitted for review.']]);
    }

    private function documentLabel(InstructorDocument $d): string
    {
        $labels = [
            'drivers_licence' => 'Driver\'s Licence (C)',
            'instructor_licence' => 'Driving Instructor\'s Licence (C)',
            'wwcc' => 'WWCC',
        ];
        $base = $labels[$d->type] ?? $d->type;
        if ($d->side) {
            return $base.' - '.ucfirst($d->side);
        }
        return $base;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\InstructorApplication;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\InstructorApplicationReceived;
use App\Notifications\InstructorApplicationAdminAlert;
use App\Services\BlockedSignupChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Public-facing "Apply to be an instructor" form.
 *
 *   GET  /apply-as-instructor
 *   POST /apply-as-instructor
 *
 * Submitting does NOT create an account or send a magic link — it just
 * registers an application. Admin reviews, then approves (which creates
 * an InstructorInvite + sends magic-link email) or rejects (sends rejection
 * email).
 */
class InstructorApplicationController extends Controller
{
    public function show()
    {
        return view('frontend.pages.instructor-application');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'       => ['required', 'string', 'max:100'],
            'last_name'        => ['nullable', 'string', 'max:100'],
            'email'            => ['required', 'email', 'max:191'],
            'phone'            => ['required', 'string', 'max:30'],

            'years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'transmission'     => ['nullable', 'in:auto,manual,both'],
            'suburb_id'        => ['nullable', 'exists:suburbs,id'],
            'lesson_price'     => ['nullable', 'numeric', 'min:0', 'max:500'],
            'vehicle_make'     => ['nullable', 'string', 'max:60'],
            'vehicle_model'    => ['nullable', 'string', 'max:60'],
            'vehicle_year'     => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'bio'              => ['nullable', 'string', 'max:2000'],

            // Compliance docs — at minimum driver licence + instructor certificate
            'driver_licence'         => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'instructor_certificate' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'wwcc'                   => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'vehicle_rego'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'insurance'              => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],

            'accept_terms'     => ['required', 'accepted'],
            // honeypot
            'website'          => ['nullable', 'string', 'max:0'],
        ]);

        // Honeypot trip — silently succeed
        if (! empty($request->input('website'))) {
            return redirect()->route('instructor-application.show')
                ->with('message', 'Thanks! Your application has been received.');
        }

        $email = strtolower(trim($data['email']));

        // Block re-applications from previously-banned applicants
        $blocked = BlockedSignupChecker::check($email, $data['phone']);
        if ($blocked) {
            BlockedSignupChecker::logAttempt($blocked, $email, $data['phone'], trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')), $request->ip(), 'instructor_application');
            return back()->withInput()->withErrors([
                'email' => 'We can\'t process this application. If you believe this is an error, please email support@securelicence.com.',
            ]);
        }

        // Refuse duplicates — only one open application per email
        $exists = InstructorApplication::where('email', $email)
            ->whereIn('status', [InstructorApplication::STATUS_PENDING, InstructorApplication::STATUS_UNDER_REVIEW])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors([
                'email' => 'You already have an application in progress for this email. We\'ll be in touch once it\'s reviewed.',
            ]);
        }

        // If an active instructor account already exists with this email, bounce them to login
        if (User::where('email', $email)->where('role', User::ROLE_INSTRUCTOR)->exists()) {
            return back()->withInput()->withErrors([
                'email' => 'An instructor account already exists with this email. Please log in instead.',
            ]);
        }

        // Upload docs to Spaces
        $documents = $this->storeDocuments($request, $email);

        $app = InstructorApplication::create([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'] ?? null,
            'email'            => $email,
            'phone'            => $data['phone'],
            'years_experience' => $data['years_experience'] ?? null,
            'transmission'     => $data['transmission'] ?? null,
            'suburb_id'        => $data['suburb_id'] ?? null,
            'lesson_price'     => $data['lesson_price'] ?? null,
            'vehicle_make'     => $data['vehicle_make'] ?? null,
            'vehicle_model'    => $data['vehicle_model'] ?? null,
            'vehicle_year'     => $data['vehicle_year'] ?? null,
            'bio'              => $data['bio'] ?? null,
            'documents'        => $documents,
            'status'           => InstructorApplication::STATUS_PENDING,
            'applied_ip'       => $request->ip(),
            'applied_user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // Auto-ack the applicant
        try {
            Notification::route('mail', $email)->notify(new InstructorApplicationReceived($app));
        } catch (\Throwable $e) {
            Log::warning('Instructor application ack email failed: ' . $e->getMessage());
        }

        // Alert admin
        try {
            $adminEmail = SiteSetting::get('admin_notification_email', 'admin@securelicence.com');
            Notification::route('mail', $adminEmail)->notify(new InstructorApplicationAdminAlert($app));
        } catch (\Throwable $e) {
            Log::warning('Instructor application admin alert failed: ' . $e->getMessage());
        }

        return redirect()->route('instructor-application.show')->with('message',
            "Thanks! Your application <strong>{$app->reference}</strong> has been received. "
            . "We'll review your documents within 2 business days and email you the outcome."
        );
    }

    /**
     * Upload provided document files to Spaces.
     * Returns an array of [doc_type => spaces_path].
     */
    private function storeDocuments(Request $request, string $email): array
    {
        $folder = 'instructor-applications/' . date('Y/m') . '/' . Str::slug($email) . '-' . Str::random(6);
        $documents = [];

        foreach (['driver_licence', 'instructor_certificate', 'wwcc', 'vehicle_rego', 'insurance'] as $field) {
            if (! $request->hasFile($field)) continue;
            $file = $request->file($field);
            $ext = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
            $path = $folder . '/' . $field . '.' . $ext;
            try {
                Storage::disk('spaces')->put($path, file_get_contents($file->getRealPath()), 'public');
                $documents[$field] = $path;
            } catch (\Throwable $e) {
                Log::warning("Instructor application doc upload failed ($field): " . $e->getMessage());
            }
        }
        return $documents;
    }
}

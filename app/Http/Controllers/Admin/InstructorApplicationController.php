<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorApplication;
use App\Models\InstructorInvite;
use App\Notifications\InstructorApplicationApproved;
use App\Notifications\InstructorApplicationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class InstructorApplicationController extends Controller
{
    public function index(Request $request)
    {
        $q = InstructorApplication::with(['reviewer', 'suburb'])->latest();

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        if ($search = trim((string) $request->query('q'))) {
            $q->where(function ($w) use ($search) {
                $w->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applications = $q->paginate(20)->withQueryString();

        $stats = [
            'pending'      => InstructorApplication::where('status', InstructorApplication::STATUS_PENDING)->count(),
            'under_review' => InstructorApplication::where('status', InstructorApplication::STATUS_UNDER_REVIEW)->count(),
            'approved'     => InstructorApplication::where('status', InstructorApplication::STATUS_APPROVED)->count(),
            'rejected'     => InstructorApplication::where('status', InstructorApplication::STATUS_REJECTED)->count(),
        ];

        return view('admin.instructor-applications.index', compact('applications', 'stats'));
    }

    public function show(InstructorApplication $instructorApplication)
    {
        $instructorApplication->load(['reviewer', 'suburb', 'invite']);

        // Generate temporary signed URLs for each document
        $docUrls = [];
        foreach ((array) $instructorApplication->documents as $type => $path) {
            try {
                $docUrls[$type] = Storage::disk('spaces')->temporaryUrl($path, now()->addMinutes(30));
            } catch (\Throwable $e) {
                $docUrls[$type] = null;
            }
        }

        return view('admin.instructor-applications.show', [
            'app'     => $instructorApplication,
            'docUrls' => $docUrls,
        ]);
    }

    public function markUnderReview(InstructorApplication $instructorApplication)
    {
        if ($instructorApplication->status !== InstructorApplication::STATUS_PENDING) {
            return back()->withErrors(['status' => 'Only pending applications can be moved to under review.']);
        }
        $instructorApplication->update([
            'status'              => InstructorApplication::STATUS_UNDER_REVIEW,
            'reviewed_by_user_id' => auth()->id(),
        ]);
        return back()->with('message', 'Marked as under review.');
    }

    public function approve(Request $request, InstructorApplication $instructorApplication)
    {
        if (in_array($instructorApplication->status, [InstructorApplication::STATUS_APPROVED, InstructorApplication::STATUS_REJECTED], true)) {
            return back()->withErrors(['status' => 'This application has already been decided.']);
        }

        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $invite = InstructorInvite::create([
            'first_name'         => $instructorApplication->first_name,
            'last_name'          => $instructorApplication->last_name,
            'email'              => $instructorApplication->email,
            'phone'              => $instructorApplication->phone,
            'years_experience'   => $instructorApplication->years_experience,
            'transmission'       => $instructorApplication->transmission,
            'bio'                => $instructorApplication->bio,
            'suburb_id'          => $instructorApplication->suburb_id,
            'lesson_price'       => $instructorApplication->lesson_price,
            'vehicle_make'       => $instructorApplication->vehicle_make,
            'vehicle_model'      => $instructorApplication->vehicle_model,
            'vehicle_year'       => $instructorApplication->vehicle_year,
            'personal_note'      => 'Application '.$instructorApplication->reference.' approved — welcome aboard!',
            'invited_by_user_id' => auth()->id(),
        ]);

        $instructorApplication->update([
            'status'              => InstructorApplication::STATUS_APPROVED,
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at'         => now(),
            'admin_notes'         => $data['admin_notes'] ?? $instructorApplication->admin_notes,
            'approved_invite_id'  => $invite->id,
        ]);

        try {
            Notification::route('mail', $instructorApplication->email)
                ->notify(new InstructorApplicationApproved($instructorApplication, $invite));
        } catch (\Throwable $e) {
            Log::warning('Instructor application approval email failed (id='.$instructorApplication->id.'): '.$e->getMessage());
            return back()->with('message', 'Application approved, but the approval email failed to send. You can resend it from the invites list.');
        }

        return redirect()->route('admin.instructor-applications.show', $instructorApplication)
            ->with('message', 'Application approved. Setup email sent to '.$instructorApplication->email.'.');
    }

    public function reject(Request $request, InstructorApplication $instructorApplication)
    {
        if (in_array($instructorApplication->status, [InstructorApplication::STATUS_APPROVED, InstructorApplication::STATUS_REJECTED], true)) {
            return back()->withErrors(['status' => 'This application has already been decided.']);
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
            'admin_notes'      => ['nullable', 'string', 'max:2000'],
        ]);

        $instructorApplication->update([
            'status'              => InstructorApplication::STATUS_REJECTED,
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at'         => now(),
            'rejection_reason'    => $data['rejection_reason'],
            'admin_notes'         => $data['admin_notes'] ?? $instructorApplication->admin_notes,
        ]);

        try {
            Notification::route('mail', $instructorApplication->email)
                ->notify(new InstructorApplicationRejected($instructorApplication));
        } catch (\Throwable $e) {
            Log::warning('Instructor application rejection email failed (id='.$instructorApplication->id.'): '.$e->getMessage());
        }

        return redirect()->route('admin.instructor-applications.show', $instructorApplication)
            ->with('message', 'Application rejected. Notification sent to applicant.');
    }
}

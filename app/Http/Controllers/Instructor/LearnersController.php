<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorLearnerInvite;
use App\Models\User;
use App\Notifications\InstructorLearnerInviteNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class LearnersController extends Controller
{
    /**
     * List unique learners who have (or had) bookings with this instructor.
     * Returns: learner details, guardian (placeholder), hours completed, upcoming count.
     */
    public function index(Request $request): JsonResponse
    {
        $instructorId = Auth::id();
        $search = $request->input('q', '');

        // Collect learner IDs from BOTH bookings AND accepted invites (so accepted-but-not-yet-booked
        // learners still show up in the My Learners list).
        $bookings = Booking::where('instructor_id', $instructorId)
            ->with('learner:id,name,email,phone')
            ->get();

        $bookingLearnerIds = $bookings->pluck('learner_id')->filter()->unique();

        $acceptedInviteLearnerIds = InstructorLearnerInvite::where('instructor_user_id', $instructorId)
            ->whereNotNull('accepted_by_user_id')
            ->pluck('accepted_by_user_id')
            ->unique();

        $allLearnerIds = $bookingLearnerIds->merge($acceptedInviteLearnerIds)->unique()->values();

        // Pre-fetch learners (covers ones with no bookings yet)
        $learnerUsers = User::whereIn('id', $allLearnerIds)->get(['id', 'name', 'email', 'phone'])->keyBy('id');

        $learners = collect();
        foreach ($allLearnerIds as $learnerId) {
            $learner = $learnerUsers->get($learnerId);
            if (! $learner) {
                continue;
            }

            if ($search !== '') {
                $term = strtolower($search);
                $match = str_contains(strtolower($learner->name ?? ''), $term)
                    || str_contains(strtolower($learner->phone ?? ''), $term)
                    || str_contains(strtolower($learner->email ?? ''), $term);
                if (! $match) {
                    continue;
                }
            }

            $learnerBookings = $bookings->where('learner_id', $learnerId);
            $hoursCompleted = $learnerBookings
                ->where('status', Booking::STATUS_COMPLETED)
                ->sum(fn ($b) => ($b->duration_minutes ?? 60) / 60);
            $upcomingCount = $learnerBookings
                ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                ->filter(fn ($b) => $b->scheduled_at && $b->scheduled_at->isFuture())
                ->count();

            $learners->push([
                'learner' => [
                    'id' => $learner->id,
                    'name' => $learner->name,
                    'email' => $learner->email,
                    'phone' => $learner->phone,
                ],
                'guardian' => null,
                'hours_completed' => round($hoursCompleted, 1),
                'upcoming_bookings' => $upcomingCount,
                'has_bookings' => $learnerBookings->isNotEmpty(),
            ]);
        }

        $learners = $learners->sortBy(fn ($l) => $l['learner']['name'] ?? '')->values();

        return response()->json(['data' => $learners]);
    }

    /**
     * Get detailed info about a specific learner (for the instructor's learner detail modal).
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $instructorId = Auth::id();

        // Allow access if learner has bookings OR has accepted an invite from this instructor
        $hasBookings = Booking::where('instructor_id', $instructorId)
            ->where('learner_id', $user->id)
            ->exists();

        $hasAcceptedInvite = InstructorLearnerInvite::where('instructor_user_id', $instructorId)
            ->where('accepted_by_user_id', $user->id)
            ->whereNotNull('accepted_at')
            ->exists();

        if (! $hasBookings && ! $hasAcceptedInvite) {
            return response()->json(['message' => 'Learner not found.'], 404);
        }

        $bookings = Booking::where('instructor_id', $instructorId)
            ->where('learner_id', $user->id)
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $hoursCompleted = $bookings->where('status', Booking::STATUS_COMPLETED)
            ->sum(fn ($b) => ($b->duration_minutes ?? 60) / 60);

        $upcomingBookings = $bookings->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->filter(fn ($b) => $b->scheduled_at && $b->scheduled_at->isFuture())
            ->values()
            ->map(fn ($b) => [
                'id' => $b->id,
                'type' => $b->type,
                'scheduled_at' => $b->scheduled_at->toIso8601String(),
                'duration_minutes' => $b->duration_minutes,
                'status' => $b->status,
            ]);

        $recentBookings = $bookings->take(10)->map(fn ($b) => [
            'id' => $b->id,
            'type' => $b->type,
            'scheduled_at' => $b->scheduled_at ? $b->scheduled_at->toIso8601String() : null,
            'duration_minutes' => $b->duration_minutes,
            'status' => $b->status,
        ]);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'joined' => $user->created_at ? $user->created_at->format('d M Y') : null,
                'hours_completed' => round($hoursCompleted, 1),
                'total_bookings' => $bookings->count(),
                'upcoming_bookings' => $upcomingBookings,
                'recent_bookings' => $recentBookings,
            ],
        ]);
    }

    /**
     * Invite a learner by email — persists invite, generates token, sends branded email.
     */
    public function invite(Request $request): JsonResponse
    {
        $instructor = Auth::user();

        // Rate limit: 20 invites per hour per instructor
        $key = 'instructor-invite:' . $instructor->id;
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return response()->json(['message' => 'Too many invites sent. Please try again later.'], 429);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $email = strtolower(trim($validated['email']));

        // Block self-invite
        if ($email === strtolower($instructor->email)) {
            return response()->json(['message' => 'You can\'t invite yourself.'], 422);
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $hasBookings = Booking::where('instructor_id', $instructor->id)
                ->where('learner_id', $existingUser->id)
                ->exists();
            if ($hasBookings) {
                return response()->json([
                    'message' => 'This learner already has bookings with you.',
                    'existing' => true,
                ], 422);
            }
        }

        // Avoid duplicate pending invite within 7 days
        $duplicate = InstructorLearnerInvite::where('instructor_user_id', $instructor->id)
            ->where('invitee_email', $email)
            ->whereNull('accepted_at')
            ->where('created_at', '>=', now()->subDays(7))
            ->exists();
        if ($duplicate) {
            return response()->json([
                'message' => 'You\'ve already invited ' . $email . ' in the last 7 days.',
            ], 422);
        }

        $invite = InstructorLearnerInvite::create([
            'instructor_user_id' => $instructor->id,
            'invitee_email' => $email,
            'invitee_name' => $validated['name'] ?? null,
            'personal_message' => $validated['message'] ?? null,
        ]);

        try {
            Notification::route('mail', $email)->notify(new InstructorLearnerInviteNotification($invite, $instructor));
            $invite->update(['email_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Instructor learner invite email failed: ' . $e->getMessage());
            $invite->delete();
            return response()->json(['message' => 'Could not send the invite right now. Please try again.'], 500);
        }

        RateLimiter::hit($key, 3600);

        return response()->json([
            'message' => 'Invitation sent to ' . $email . '!',
            'data' => [
                'id' => $invite->id,
                'email' => $email,
                'name' => $validated['name'] ?? null,
                'already_registered' => $existingUser !== null,
            ],
        ]);
    }

    /**
     * List pending invites this instructor has sent (not yet accepted).
     */
    public function pendingInvites(Request $request): JsonResponse
    {
        $invites = InstructorLearnerInvite::where('instructor_user_id', Auth::id())
            ->whereNull('accepted_at')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function (InstructorLearnerInvite $i) {
                return [
                    'id' => $i->id,
                    'invitee_email' => $i->invitee_email,
                    'invitee_name' => $i->invitee_name,
                    'personal_message' => $i->personal_message,
                    'sent_at' => $i->email_sent_at?->toIso8601String() ?? $i->created_at->toIso8601String(),
                    'expires_at' => $i->expires_at?->toIso8601String(),
                    'is_expired' => $i->isExpired(),
                ];
            });

        return response()->json(['data' => $invites]);
    }

    /**
     * Resend a pending invite email.
     */
    public function resendInvite(Request $request, InstructorLearnerInvite $invite): JsonResponse
    {
        if ($invite->instructor_user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        if ($invite->isAccepted()) {
            return response()->json(['message' => 'This invite has already been accepted.'], 422);
        }

        try {
            Notification::route('mail', $invite->invitee_email)
                ->notify(new InstructorLearnerInviteNotification($invite, Auth::user()));
            $invite->update(['email_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Resend invite failed: ' . $e->getMessage());
            return response()->json(['message' => 'Could not resend the invite.'], 500);
        }

        return response()->json(['message' => 'Invite resent to ' . $invite->invitee_email]);
    }

    /**
     * Cancel a pending invite.
     */
    public function cancelInvite(Request $request, InstructorLearnerInvite $invite): JsonResponse
    {
        if ($invite->instructor_user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $invite->delete();
        return response()->json(['message' => 'Invite cancelled.']);
    }

    /**
     * Public landing page for the invite link — accepts the invite + redirects to register/find-instructor.
     */
    public function acceptInvite(Request $request, string $token)
    {
        $invite = InstructorLearnerInvite::where('invite_token', $token)->first();

        if (! $invite || $invite->isExpired()) {
            return redirect()->route('find-instructor')
                ->with('message', 'This invite link is invalid or has expired.');
        }

        $instructorProfile = $invite->instructor->instructorProfile;
        $instructorProfileId = $instructorProfile?->id;

        // If user is logged in, link them + redirect to booking
        if (Auth::check()) {
            $user = Auth::user();
            if (strtolower($user->email) === strtolower($invite->invitee_email)) {
                if (! $invite->isAccepted()) {
                    $invite->update([
                        'accepted_by_user_id' => $user->id,
                        'accepted_at' => now(),
                    ]);
                }
                if ($instructorProfileId) {
                    return redirect()->route('learner.bookings.new', ['instructor_profile_id' => $instructorProfileId])
                        ->with('message', 'Invite accepted! Book your first lesson with ' . $invite->instructor->name . '.');
                }
            }
        }

        // Existing user but not logged in: send to login
        $existingUser = User::where('email', $invite->invitee_email)->first();
        if ($existingUser) {
            session(['accept_instructor_invite_token' => $token]);
            return redirect()->route('learner.login')
                ->with('message', 'You\'ve been invited by ' . $invite->instructor->name . '. Log in to accept.');
        }

        // New user: route to registration with prefilled email + invite token
        session(['accept_instructor_invite_token' => $token]);
        return redirect()->route('register', [
            'invited_email' => $invite->invitee_email,
            'invited_name' => $invite->invitee_name,
            'role' => 'learner',
        ])->with('message', 'You\'ve been invited by ' . $invite->instructor->name . '. Sign up to accept.');
    }
}

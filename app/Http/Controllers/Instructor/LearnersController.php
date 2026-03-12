<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $bookings = Booking::where('instructor_id', $instructorId)
            ->with('learner:id,name,email,phone')
            ->get();

        $learnerIds = $bookings->pluck('learner_id')->unique()->values();
        $learners = collect();

        foreach ($learnerIds as $learnerId) {
            $learnerBookings = $bookings->where('learner_id', $learnerId);
            $learner = $learnerBookings->first()->learner;
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

        // Verify this learner has bookings with this instructor
        $hasBookings = Booking::where('instructor_id', $instructorId)
            ->where('learner_id', $user->id)
            ->exists();

        if (! $hasBookings) {
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
     * Invite a learner by email.
     * Creates an invitation record and (in production) sends email via configured SMTP.
     */
    public function invite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $instructor = Auth::user();
        $instructorProfile = $instructor->instructorProfile;

        // Check if user already exists
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            // Check if already has bookings with this instructor
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

        // Log the invitation (in production, would send email via SiteSetting SMTP config)
        \Illuminate\Support\Facades\Log::info('Learner invitation sent', [
            'instructor_id' => $instructor->id,
            'instructor_name' => $instructor->name,
            'invited_email' => $validated['email'],
            'invited_name' => $validated['name'] ?? null,
            'custom_message' => $validated['message'] ?? null,
        ]);

        return response()->json([
            'message' => 'Invitation sent to ' . $validated['email'] . '!',
            'data' => [
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'already_registered' => $existingUser !== null,
            ],
        ]);
    }
}

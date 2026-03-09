<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
}

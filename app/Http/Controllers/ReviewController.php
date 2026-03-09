<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a review for a completed booking (learner only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        $booking = Booking::findOrFail($request->input('booking_id'));

        if ($booking->learner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($booking->status !== Booking::STATUS_COMPLETED) {
            return response()->json(['message' => 'You can only review completed bookings.'], 422);
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            return response()->json(['message' => 'You have already reviewed this booking.'], 422);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'learner_id' => $user->id,
            'instructor_id' => $booking->instructor_id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        $review->load(['booking', 'learner', 'instructor']);

        return response()->json([
            'data' => [
                'id' => $review->id,
                'booking_id' => $review->booking_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toIso8601String(),
            ],
        ], 201);
    }
}

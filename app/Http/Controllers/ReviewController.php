<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a review for a completed booking (learner only).
     * Reviews are created with status 'pending' and require admin approval.
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

        // Basic content validation — reject reviews with only special characters
        $comment = $request->input('comment');
        if ($comment && strlen(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $comment))) < 3) {
            return response()->json(['message' => 'Please write a meaningful review comment.'], 422);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'learner_id' => $user->id,
            'instructor_id' => $booking->instructor_id,
            'rating' => $request->input('rating'),
            'comment' => $comment,
            'status' => Review::STATUS_PENDING, // Requires admin approval
        ]);

        $review->load(['booking', 'learner', 'instructor']);

        // Build Google Reviews redirect URL if configured
        $googlePlaceId = SiteSetting::get('google_place_id');
        $googleReviewUrl = null;
        if ($googlePlaceId) {
            $googleReviewUrl = 'https://search.google.com/local/writereview?placeid=' . urlencode($googlePlaceId);
        }

        return response()->json([
            'data' => [
                'id' => $review->id,
                'booking_id' => $review->booking_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'status' => $review->status,
                'created_at' => $review->created_at->toIso8601String(),
            ],
            'message' => 'Thank you for your review! It will be visible after admin approval.',
            'google_review_url' => $googleReviewUrl,
            'google_review_prefill' => $comment, // Text learner can copy to Google
        ], 201);
    }

    /**
     * Mark that the learner was prompted to post on Google Reviews.
     */
    public function markGooglePrompted(Review $review): JsonResponse
    {
        $user = Auth::user();
        if ($review->learner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $review->update(['google_review_prompted' => true]);

        return response()->json(['message' => 'Google review prompt tracked.']);
    }
}

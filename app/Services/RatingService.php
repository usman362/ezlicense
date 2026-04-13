<?php

namespace App\Services;

use App\Models\InstructorAuditLog;
use App\Models\InstructorProfile;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RatingService
{
    /**
     * Base rating points for every new instructor.
     */
    const BASE_POINTS = 4.0;

    /**
     * Points awarded per completed lesson.
     */
    const POINTS_PER_LESSON = 4.0;

    /**
     * Recovery costs: how many 5-star reviews are needed to recover
     * after receiving a review of each star level.
     */
    const RECOVERY_MAP = [
        1 => 10, // 1-star needs 10x 5-star to recover
        2 => 5,  // 2-star needs 5x 5-star to recover
        3 => 2,  // 3-star needs 2x 5-star to recover
        4 => 2,  // 4-star needs 2x 5-star to recover
    ];

    /**
     * Record that a lesson (booking) was completed.
     * Increments the lesson counter, adds lesson points, and recalculates the rating.
     */
    public function recordLessonCompleted(InstructorProfile $profile): void
    {
        $profile->increment('total_completed_lessons');
        $profile->increment('rating_points', self::POINTS_PER_LESSON);

        $this->recalculateRating($profile);
    }

    /**
     * Process a newly-approved review and update the instructor's weighted rating.
     */
    public function processNewReview(InstructorProfile $profile, Review $review): void
    {
        $rating = (int) $review->rating;

        if ($rating === 5) {
            // 5-star review: increment consecutive streak
            $profile->increment('consecutive_five_stars');

            // If the instructor is in recovery deficit, each 5-star reduces it by 1
            if ($profile->recovery_deficit > 0) {
                $profile->decrement('recovery_deficit');
            }
        } else {
            // Less than 5-star: reset consecutive streak
            $profile->update(['consecutive_five_stars' => 0]);

            // Add recovery cost based on the star level
            $recoveryCost = self::RECOVERY_MAP[$rating] ?? 0;
            if ($recoveryCost > 0) {
                $profile->increment('recovery_deficit', $recoveryCost);
            }

            // Reduce rating points proportionally to how bad the review is
            $penalty = (5 - $rating) * 0.5;
            $profile->decrement('rating_points', $penalty);
        }

        $this->recalculateRating($profile);
    }

    /**
     * Full recalculation of the weighted rating from all approved reviews.
     *
     * Algorithm:
     * 1. Start with BASE_POINTS (4.0)
     * 2. Add lesson bonus: min(total_completed_lessons * 0.02, 0.5)
     * 3. Calculate review average from approved, visible reviews
     * 4. If 5+ reviews: blend 60% review_average + 40% base_with_bonus
     *    If fewer: weight more towards base
     * 5. If consecutive_five_stars >= 10: add 0.1 (capped at 5.0)
     * 6. If recovery_deficit > 0: subtract min(recovery_deficit * 0.05, 0.5)
     * 7. Clamp between 1.0 and 5.0
     * 8. Save to weighted_rating
     */
    public function recalculateRating(InstructorProfile $profile): void
    {
        $profile->refresh();

        // Step 1: Base points
        $base = self::BASE_POINTS;

        // Step 2: Lesson bonus — each lesson adds 0.02, max +0.5
        $lessonBonus = min($profile->total_completed_lessons * 0.02, 0.5);
        $baseWithBonus = $base + $lessonBonus;

        // Step 3: Calculate average from approved, visible reviews
        $reviewAvg = (float) $profile->reviews()->public()->avg('rating');
        $reviewCount = $profile->reviews()->public()->count();

        // Step 4: Blend based on number of reviews
        if ($reviewCount >= 5) {
            // 60% review average + 40% base with bonus
            $blended = ($reviewAvg * 0.6) + ($baseWithBonus * 0.4);
        } elseif ($reviewCount > 0) {
            // Gradually increase review weight: each review adds ~12% weight
            $reviewWeight = $reviewCount * 0.12;
            $baseWeight = 1.0 - $reviewWeight;
            $blended = ($reviewAvg * $reviewWeight) + ($baseWithBonus * $baseWeight);
        } else {
            // No reviews yet — use base with bonus
            $blended = $baseWithBonus;
        }

        // Step 5: Consecutive 5-star bonus
        if ($profile->consecutive_five_stars >= 10) {
            $blended += 0.1;
        }

        // Step 6: Recovery penalty
        if ($profile->recovery_deficit > 0) {
            $penalty = min($profile->recovery_deficit * 0.05, 0.5);
            $blended -= $penalty;
        }

        // Step 7: Clamp between 1.0 and 5.0
        $finalRating = max(1.0, min(5.0, round($blended, 2)));

        // Step 8: Save
        $profile->update(['weighted_rating' => $finalRating]);
    }

    /**
     * Admin directly overrides the weighted rating.
     * Logs the change in the audit trail.
     */
    public function adminAdjustRating(InstructorProfile $profile, float $newRating, ?string $reason = null): void
    {
        $oldRating = $profile->weighted_rating;

        // Clamp the admin-supplied value
        $newRating = max(1.0, min(5.0, round($newRating, 2)));

        $profile->update([
            'weighted_rating' => $newRating,
        ]);

        InstructorAuditLog::record(
            $profile->id,
            Auth::id(),
            'rating_adjusted',
            "Rating manually adjusted from {$oldRating} to {$newRating}" . ($reason ? ": {$reason}" : ''),
            ['weighted_rating' => $oldRating],
            ['weighted_rating' => $newRating, 'reason' => $reason],
        );

        Log::info("Admin rating adjustment for instructor profile #{$profile->id}: {$oldRating} → {$newRating}", [
            'admin_id' => Auth::id(),
            'reason' => $reason,
        ]);
    }

    /**
     * Called when a review is deleted by admin.
     * Recalculates everything from scratch.
     */
    public function onReviewDeleted(InstructorProfile $profile): void
    {
        // Recalculate consecutive 5-star streak from remaining approved reviews
        $approvedReviews = $profile->reviews()
            ->public()
            ->orderBy('created_at', 'desc')
            ->pluck('rating');

        $consecutive = 0;
        foreach ($approvedReviews as $rating) {
            if ((int) $rating === 5) {
                $consecutive++;
            } else {
                break;
            }
        }

        // Recalculate recovery deficit from remaining approved reviews
        $deficit = 0;
        foreach ($approvedReviews as $rating) {
            $r = (int) $rating;
            if ($r === 5) {
                $deficit = max(0, $deficit - 1);
            } elseif (isset(self::RECOVERY_MAP[$r])) {
                $deficit += self::RECOVERY_MAP[$r];
            }
        }

        $profile->update([
            'consecutive_five_stars' => $consecutive,
            'recovery_deficit' => max(0, $deficit),
        ]);

        $this->recalculateRating($profile);
    }
}

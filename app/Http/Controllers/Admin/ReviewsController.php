<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorAuditLog;
use App\Models\Review;
use App\Notifications\ReviewApproved;
use App\Services\RatingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Centralized admin control panel for ALL reviews across the platform.
 * Approve/reject/hide/delete + bulk actions + full filtering.
 */
class ReviewsController extends Controller
{
    public function __construct(private RatingService $ratingService)
    {
    }

    public function index(Request $request): View
    {
        $query = Review::with([
            'booking:id,scheduled_at,type',
            'learner:id,name,email',
            'instructor:id,name,email',
            'instructor.instructorProfile:id,user_id',
            'moderator:id,name',
        ])->latest();

        // Filters
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($rating = (int) $request->input('rating')) {
            $query->where('rating', $rating);
        }
        if ($visibility = $request->input('visibility')) {
            if ($visibility === 'visible') {
                $query->where('is_hidden', false);
            } elseif ($visibility === 'hidden') {
                $query->where('is_hidden', true);
            }
        }
        if ($googlePrompt = $request->input('google_prompt')) {
            if ($googlePrompt === 'yes') {
                $query->where('google_review_prompted', true);
            } elseif ($googlePrompt === 'no') {
                $query->where('google_review_prompted', false);
            }
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('learner', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('instructor', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }
        if ($days = (int) $request->input('days')) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $reviews = $query->paginate(30)->withQueryString();

        // KPI stats
        $stats = [
            'total'         => Review::count(),
            'pending'       => Review::where('status', 'pending')->count(),
            'approved'      => Review::where('status', 'approved')->count(),
            'rejected'      => Review::where('status', 'rejected')->count(),
            'hidden'        => Review::where('is_hidden', true)->count(),
            'five_star'     => Review::where('rating', 5)->where('status', 'approved')->count(),
            'google_prompted' => Review::where('google_review_prompted', true)->count(),
            'avg_rating'    => round((float) Review::where('status', 'approved')->avg('rating') ?: 0, 2),
            'last_7_days'   => Review::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        if ($review->status === 'approved') {
            return back()->with('error', 'Review is already approved.');
        }

        $review->update([
            'status' => 'approved',
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        // Notify instructor + recalc rating
        try {
            $review->instructor?->notify(new ReviewApproved($review));
            $instructorProfile = $review->instructor?->instructorProfile;
            if ($instructorProfile) {
                $this->ratingService->processNewReview($instructorProfile, $review);
            }
        } catch (\Throwable $e) {
            \Log::warning('Review approval side-effects failed: ' . $e->getMessage());
        }

        $this->log($review, 'review_approved', "Review #{$review->id} approved", [
            'rating' => $review->rating,
            'learner' => $review->learner?->name,
        ]);

        return back()->with('message', 'Review approved and is now visible publicly.');
    }

    public function reject(Request $request, Review $review): RedirectResponse
    {
        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $review->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
        ]);

        $this->log($review, 'review_rejected', "Review #{$review->id} rejected", [
            'reason' => $request->input('rejection_reason'),
        ]);

        return back()->with('message', 'Review rejected.');
    }

    public function toggleVisibility(Review $review): RedirectResponse
    {
        if ($review->status !== 'approved') {
            return back()->with('error', 'Only approved reviews can be hidden.');
        }

        $review->update(['is_hidden' => ! $review->is_hidden]);

        // Recalculate rating since hidden reviews shouldn't count
        try {
            $instructorProfile = $review->instructor?->instructorProfile;
            if ($instructorProfile) {
                $this->ratingService->recalculateRating($instructorProfile);
            }
        } catch (\Throwable $e) {
            \Log::warning('Rating recalc after visibility toggle failed: ' . $e->getMessage());
        }

        $this->log($review, 'review_visibility_toggled', "Review #{$review->id} " . ($review->is_hidden ? 'hidden' : 'unhidden'), [
            'is_hidden' => $review->is_hidden,
        ]);

        return back()->with('message', $review->is_hidden ? 'Review hidden from public.' : 'Review visible publicly again.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $instructorProfile = $review->instructor?->instructorProfile;
        $meta = ['rating' => $review->rating, 'comment' => $review->comment];

        $review->delete();

        // Recalculate rating from scratch
        try {
            if ($instructorProfile) {
                $this->ratingService->onReviewDeleted($instructorProfile);
            }
        } catch (\Throwable $e) {
            \Log::warning('Rating recalc after delete failed: ' . $e->getMessage());
        }

        if ($instructorProfile) {
            try {
                InstructorAuditLog::record(
                    $instructorProfile->id,
                    Auth::id(),
                    'review_deleted',
                    "Review deleted (rating: {$meta['rating']})",
                    null,
                    $meta,
                );
            } catch (\Throwable $e) {
                // Audit log is best-effort
            }
        }

        return back()->with('message', 'Review permanently deleted.');
    }

    /**
     * Bulk action: approve/reject/delete multiple reviews at once.
     */
    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,delete,hide,unhide',
            'review_ids' => 'required|array|min:1|max:100',
            'review_ids.*' => 'integer|exists:reviews,id',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $reviews = Review::whereIn('id', $validated['review_ids'])->get();
        $count = 0;

        foreach ($reviews as $review) {
            try {
                switch ($validated['action']) {
                    case 'approve':
                        if ($review->status !== 'approved') {
                            $review->update([
                                'status' => 'approved',
                                'moderated_at' => now(),
                                'moderated_by' => Auth::id(),
                                'rejection_reason' => null,
                            ]);
                            $review->instructor?->notify(new ReviewApproved($review));
                            $instructorProfile = $review->instructor?->instructorProfile;
                            if ($instructorProfile) {
                                $this->ratingService->processNewReview($instructorProfile, $review);
                            }
                            $count++;
                        }
                        break;

                    case 'reject':
                        $review->update([
                            'status' => 'rejected',
                            'rejection_reason' => $validated['rejection_reason'] ?? 'Bulk-rejected by admin',
                            'moderated_at' => now(),
                            'moderated_by' => Auth::id(),
                        ]);
                        $count++;
                        break;

                    case 'hide':
                        if ($review->status === 'approved' && ! $review->is_hidden) {
                            $review->update(['is_hidden' => true]);
                            $count++;
                        }
                        break;

                    case 'unhide':
                        if ($review->is_hidden) {
                            $review->update(['is_hidden' => false]);
                            $count++;
                        }
                        break;

                    case 'delete':
                        $review->delete();
                        $count++;
                        break;
                }
            } catch (\Throwable $e) {
                \Log::warning("Bulk action {$validated['action']} failed for review #{$review->id}: " . $e->getMessage());
            }
        }

        return back()->with('message', "Bulk {$validated['action']}: applied to {$count} review(s).");
    }

    private function log(Review $review, string $action, string $summary, array $metadata = []): void
    {
        $instructorProfile = $review->instructor?->instructorProfile;
        if (! $instructorProfile) {
            return;
        }
        try {
            InstructorAuditLog::record(
                $instructorProfile->id,
                Auth::id(),
                $action,
                $summary,
                null,
                array_merge(['review_id' => $review->id], $metadata),
            );
        } catch (\Throwable $e) {
            // Audit log is best-effort
        }
    }
}

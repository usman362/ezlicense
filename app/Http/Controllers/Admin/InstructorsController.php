<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorDocument;
use App\Models\InstructorProfile;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ReviewApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorsController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorProfile::with(['user', 'documents']);

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('verification')) {
            $query->where('verification_status', $status);
        }

        if ($request->input('active') === '1') {
            $query->where('is_active', true);
        } elseif ($request->input('active') === '0') {
            $query->where('is_active', false);
        }

        $instructors = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.instructors.index', ['instructors' => $instructors]);
    }

    public function updateVerification(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'verification_status' => 'required|in:pending,documents_submitted,verified,rejected',
            'admin_notes'         => 'nullable|string|max:1000',
        ]);

        $instructorProfile->update([
            'verification_status' => $request->input('verification_status'),
            'admin_notes'         => $request->input('admin_notes'),
        ]);

        $name = $instructorProfile->user->name ?? 'Instructor';
        return redirect()->back()->with('message', "{$name}'s verification status updated to " . ucfirst($request->input('verification_status')) . ".");
    }

    public function toggleActive(InstructorProfile $instructorProfile)
    {
        $instructorProfile->is_active = ! $instructorProfile->is_active;
        $instructorProfile->save();

        $name = $instructorProfile->user->name ?? 'Instructor';
        return redirect()->back()->with('message', "{$name}'s profile has been " . ($instructorProfile->is_active ? 'activated' : 'deactivated') . ".");
    }

    public function updateDocumentStatus(Request $request, InstructorDocument $instructorDocument)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
        ]);

        $data = ['status' => $request->input('status')];
        if ($request->input('status') === 'verified') {
            $data['verified_at'] = now();
        } else {
            $data['verified_at'] = null;
        }

        $instructorDocument->update($data);

        return redirect()->back()->with('message', 'Document ' . $request->input('status') . ' successfully.');
    }

    public function show(InstructorProfile $instructorProfile)
    {
        $instructorProfile->load(['user', 'documents', 'serviceAreas', 'reviews.learner', 'reviews.booking']);

        $stats = [
            'total_bookings' => $instructorProfile->bookings()->count(),
            'completed_bookings' => $instructorProfile->bookings()->where('status', 'completed')->count(),
            'cancelled_bookings' => $instructorProfile->bookings()->where('status', 'cancelled')->count(),
            'average_rating' => $instructorProfile->averageRating(),
            'reviews_count' => $instructorProfile->reviewsCount(),
            'pending_reviews_count' => $instructorProfile->pendingReviewsCount(),
            'total_earnings' => $instructorProfile->bookings()->where('status', 'completed')->sum('amount'),
        ];

        return view('admin.instructors.show', [
            'instructor' => $instructorProfile,
            'stats' => $stats,
        ]);
    }

    /**
     * Approve a pending review — makes it visible publicly.
     * Notifies the instructor about the new review.
     */
    public function approveReview(Review $review)
    {
        if (! $review->isPending()) {
            return redirect()->back()->with('message', 'Review has already been moderated.');
        }

        $review->update([
            'status' => Review::STATUS_APPROVED,
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
        ]);

        // Notify the instructor about the approved review
        try {
            $instructor = User::find($review->instructor_id);
            if ($instructor) {
                $review->load('learner');
                $instructor->notify(new ReviewApproved($review));
            }
        } catch (\Throwable $e) {
            Log::warning('Review approval notification failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('message', 'Review approved and is now visible publicly.');
    }

    /**
     * Reject a pending review — it will not be shown publicly.
     */
    public function rejectReview(Request $request, Review $review)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $review->update([
            'status' => Review::STATUS_REJECTED,
            'rejection_reason' => $request->input('rejection_reason'),
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('message', 'Review has been rejected.');
    }

    public function deleteReview(Review $review)
    {
        $review->delete();
        return redirect()->back()->with('message', 'Review deleted successfully.');
    }

    public function toggleReviewVisibility(Review $review)
    {
        $review->is_hidden = !$review->is_hidden;
        $review->save();
        return redirect()->back()->with('message', 'Review ' . ($review->is_hidden ? 'hidden' : 'unhidden') . ' successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorAdminNote;
use App\Models\InstructorAuditLog;
use App\Models\InstructorBlock;
use App\Models\InstructorComplaint;
use App\Models\InstructorCorrespondence;
use App\Models\InstructorDocument;
use App\Models\InstructorProfile;
use App\Models\InstructorWarning;
use App\Models\Review;
use App\Models\User;
use App\Notifications\InstructorVerificationUpdated;
use App\Notifications\ReviewApproved;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $old = [
            'verification_status' => $instructorProfile->verification_status,
            'admin_notes'         => $instructorProfile->admin_notes,
        ];

        $newStatus = $request->input('verification_status');
        $adminNotes = $request->input('admin_notes');

        $instructorProfile->update([
            'verification_status' => $newStatus,
            'admin_notes'         => $adminNotes,
            // Automatically activate instructor when verified
            'is_active'           => $newStatus === 'verified' ? true : $instructorProfile->is_active,
        ]);

        InstructorAuditLog::record(
            $instructorProfile->id,
            Auth::id(),
            'verification_updated',
            'Verification status changed to ' . $newStatus,
            $old,
            ['verification_status' => $newStatus, 'admin_notes' => $adminNotes],
        );

        // Notify instructor via email — only for user-visible status changes
        if ($instructorProfile->user && $old['verification_status'] !== $newStatus
            && in_array($newStatus, ['verified', 'rejected', 'documents_submitted'])) {
            try {
                $instructorProfile->user->notify(new InstructorVerificationUpdated($newStatus, $adminNotes));
            } catch (\Throwable $e) {
                Log::warning('Instructor verification email failed: ' . $e->getMessage());
            }
        }

        $name = $instructorProfile->user->name ?? 'Instructor';
        return redirect()->back()->with('message', "{$name}'s verification status updated to " . ucfirst($newStatus) . ".");
    }

    public function toggleActive(InstructorProfile $instructorProfile)
    {
        $instructorProfile->is_active = ! $instructorProfile->is_active;
        $instructorProfile->save();

        InstructorAuditLog::record(
            $instructorProfile->id,
            Auth::id(),
            'toggled_active',
            'Profile ' . ($instructorProfile->is_active ? 'activated' : 'deactivated'),
            null,
            ['is_active' => $instructorProfile->is_active],
        );

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

        InstructorAuditLog::record(
            $instructorDocument->instructor_profile_id,
            Auth::id(),
            'document_' . $request->input('status'),
            'Document "' . $instructorDocument->type . '" marked ' . $request->input('status'),
            null,
            ['document_id' => $instructorDocument->id, 'status' => $request->input('status')],
        );

        return redirect()->back()->with('message', 'Document ' . $request->input('status') . ' successfully.');
    }

    public function show(InstructorProfile $instructorProfile)
    {
        $instructorProfile->load([
            'user',
            'documents',
            'serviceAreas',
            'reviews.learner',
            'reviews.booking',
            'blocks.admin',
            'blocks.lifter',
            'warnings.admin',
            'complaints.reporter',
            'complaints.creator',
            'complaints.resolver',
            'adminNotes.admin',
            'correspondences.admin',
        ]);

        $auditLogs = $instructorProfile->auditLogs()->with('admin')->limit(100)->get();

        $stats = [
            'total_bookings' => $instructorProfile->bookings()->count(),
            'completed_bookings' => $instructorProfile->bookings()->where('status', 'completed')->count(),
            'cancelled_bookings' => $instructorProfile->bookings()->where('status', 'cancelled')->count(),
            'average_rating' => $instructorProfile->averageRating(),
            'reviews_count' => $instructorProfile->reviewsCount(),
            'pending_reviews_count' => $instructorProfile->pendingReviewsCount(),
            'total_earnings' => $instructorProfile->bookings()->where('status', 'completed')->sum('amount'),
            'open_complaints_count' => $instructorProfile->complaints()->whereIn('status', ['open', 'investigating', 'escalated'])->count(),
            'total_warnings_count' => $instructorProfile->warnings()->count(),
            'is_blocked' => $instructorProfile->isBlocked(),
            'current_block' => $instructorProfile->currentBlock(),
        ];

        return view('admin.instructors.show', [
            'instructor' => $instructorProfile,
            'stats'      => $stats,
            'auditLogs'  => $auditLogs,
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

        $profile = InstructorProfile::where('user_id', $review->instructor_id)->first();
        if ($profile) {
            InstructorAuditLog::record(
                $profile->id,
                Auth::id(),
                'review_approved',
                'Approved ' . $review->rating . '★ review from ' . ($review->learner->name ?? 'learner'),
                null,
                ['review_id' => $review->id, 'rating' => $review->rating],
            );

            // Update weighted rating based on the new review
            try {
                app(RatingService::class)->processNewReview($profile, $review);
            } catch (\Throwable $e) {
                Log::warning('Rating recalculation failed after review approval: ' . $e->getMessage());
            }
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

        $profile = InstructorProfile::where('user_id', $review->instructor_id)->first();
        if ($profile) {
            InstructorAuditLog::record(
                $profile->id,
                Auth::id(),
                'review_rejected',
                'Rejected ' . $review->rating . '★ review',
                null,
                ['review_id' => $review->id, 'reason' => $request->input('rejection_reason')],
            );
        }

        return redirect()->back()->with('message', 'Review has been rejected.');
    }

    public function deleteReview(Review $review)
    {
        $profile = InstructorProfile::where('user_id', $review->instructor_id)->first();
        $reviewMeta = ['review_id' => $review->id, 'rating' => $review->rating, 'comment' => $review->comment];

        $review->delete();

        if ($profile) {
            InstructorAuditLog::record(
                $profile->id,
                Auth::id(),
                'review_deleted',
                'Deleted a ' . ($reviewMeta['rating'] ?? '?') . '★ review',
                null,
                $reviewMeta,
            );

            // Recalculate weighted rating from scratch after review deletion
            try {
                app(RatingService::class)->onReviewDeleted($profile);
            } catch (\Throwable $e) {
                Log::warning('Rating recalculation failed after review deletion: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('message', 'Review deleted successfully.');
    }

    public function toggleReviewVisibility(Review $review)
    {
        $review->is_hidden = !$review->is_hidden;
        $review->save();

        $profile = InstructorProfile::where('user_id', $review->instructor_id)->first();
        if ($profile) {
            InstructorAuditLog::record(
                $profile->id,
                Auth::id(),
                'review_visibility_toggled',
                $review->is_hidden ? 'Hid a review from public' : 'Unhid a review',
                null,
                ['review_id' => $review->id, 'is_hidden' => $review->is_hidden],
            );
        }

        return redirect()->back()->with('message', 'Review ' . ($review->is_hidden ? 'hidden' : 'unhidden') . ' successfully.');
    }

    // ==================================================================
    //  RATING ADJUSTMENT
    // ==================================================================

    /**
     * Admin manually adjusts an instructor's weighted rating.
     */
    public function adjustRating(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'weighted_rating' => 'required|numeric|min:1|max:5',
            'reason'          => 'nullable|string|max:1000',
        ]);

        app(RatingService::class)->adminAdjustRating(
            $instructorProfile,
            (float) $request->input('weighted_rating'),
            $request->input('reason'),
        );

        $name = $instructorProfile->user->name ?? 'Instructor';

        return redirect()->back()->with('message', "{$name}'s rating has been adjusted to " . $request->input('weighted_rating') . ".");
    }

    // ==================================================================
    //  BLOCKS
    // ==================================================================

    public function storeBlock(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'duration_type'  => 'required|in:30_days,60_days,90_days,custom,permanent',
            'custom_days'    => 'nullable|integer|min:1|max:3650',
            'reason'         => 'required|string|max:1000',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        if ($instructorProfile->isBlocked()) {
            return redirect()->back()->with('message', 'Instructor is already blocked. Lift the current block first.');
        }

        $startedAt = now();
        $expiresAt = match ($request->input('duration_type')) {
            '30_days'   => $startedAt->copy()->addDays(30),
            '60_days'   => $startedAt->copy()->addDays(60),
            '90_days'   => $startedAt->copy()->addDays(90),
            'custom'    => $startedAt->copy()->addDays((int) $request->input('custom_days', 30)),
            'permanent' => null,
            default     => null,
        };

        DB::transaction(function () use ($instructorProfile, $request, $startedAt, $expiresAt) {
            $block = InstructorBlock::create([
                'instructor_profile_id' => $instructorProfile->id,
                'admin_id'              => Auth::id(),
                'duration_type'         => $request->input('duration_type'),
                'started_at'            => $startedAt,
                'expires_at'            => $expiresAt,
                'reason'                => $request->input('reason'),
                'internal_notes'        => $request->input('internal_notes'),
            ]);

            // Deactivate the profile and mark the user
            $instructorProfile->update(['is_active' => false]);
            if ($instructorProfile->user) {
                $instructorProfile->user->update([
                    'is_active'           => false,
                    'blocked_until'       => $expiresAt,
                    'deactivation_reason' => 'blocked: ' . $request->input('reason'),
                    'deactivated_at'      => $startedAt,
                ]);
            }

            InstructorAuditLog::record(
                $instructorProfile->id,
                Auth::id(),
                'blocked',
                'Blocked — ' . ($expiresAt ? 'until ' . $expiresAt->format('d M Y') : 'permanent'),
                null,
                ['block_id' => $block->id, 'duration_type' => $request->input('duration_type'), 'reason' => $request->input('reason')],
            );
        });

        return redirect()->back()->with('message', 'Instructor has been blocked.');
    }

    public function liftBlock(Request $request, InstructorBlock $instructorBlock)
    {
        $request->validate([
            'lifted_reason' => 'nullable|string|max:1000',
        ]);

        if ($instructorBlock->lifted_at) {
            return redirect()->back()->with('message', 'Block is already lifted.');
        }

        DB::transaction(function () use ($instructorBlock, $request) {
            $instructorBlock->update([
                'lifted_at'     => now(),
                'lifted_by'     => Auth::id(),
                'lifted_reason' => $request->input('lifted_reason'),
            ]);

            $profile = $instructorBlock->instructorProfile;
            $profile->update(['is_active' => true]);
            if ($profile->user) {
                $profile->user->update([
                    'is_active'           => true,
                    'blocked_until'       => null,
                    'deactivation_reason' => null,
                    'deactivated_at'      => null,
                ]);
            }

            InstructorAuditLog::record(
                $profile->id,
                Auth::id(),
                'block_lifted',
                'Block lifted',
                null,
                ['block_id' => $instructorBlock->id, 'reason' => $request->input('lifted_reason')],
            );
        });

        return redirect()->back()->with('message', 'Block has been lifted.');
    }

    // ==================================================================
    //  WARNINGS
    // ==================================================================

    public function storeWarning(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'severity'             => 'required|in:low,medium,high,critical',
            'category'             => 'nullable|string|max:100',
            'subject'              => 'required|string|max:255',
            'description'          => 'required|string|max:5000',
            'internal_notes'       => 'nullable|string|max:2000',
            'related_complaint_id' => 'nullable|exists:instructor_complaints,id',
            'related_booking_id'   => 'nullable|integer',
            'notified_instructor'  => 'nullable|boolean',
        ]);

        $warning = InstructorWarning::create([
            'instructor_profile_id' => $instructorProfile->id,
            'admin_id'              => Auth::id(),
            'severity'              => $request->input('severity'),
            'category'              => $request->input('category'),
            'subject'               => $request->input('subject'),
            'description'           => $request->input('description'),
            'internal_notes'        => $request->input('internal_notes'),
            'related_complaint_id'  => $request->input('related_complaint_id'),
            'related_booking_id'    => $request->input('related_booking_id'),
            'notified_instructor'   => (bool) $request->input('notified_instructor', false),
            'notified_at'           => $request->input('notified_instructor') ? now() : null,
        ]);

        InstructorAuditLog::record(
            $instructorProfile->id,
            Auth::id(),
            'warning_issued',
            ucfirst($request->input('severity')) . ' warning: ' . $request->input('subject'),
            null,
            ['warning_id' => $warning->id, 'severity' => $request->input('severity')],
        );

        return redirect()->back()->with('message', 'Warning recorded.');
    }

    public function deleteWarning(InstructorWarning $instructorWarning)
    {
        $profileId = $instructorWarning->instructor_profile_id;
        $meta = ['warning_id' => $instructorWarning->id, 'subject' => $instructorWarning->subject];
        $instructorWarning->delete();

        InstructorAuditLog::record(
            $profileId,
            Auth::id(),
            'warning_deleted',
            'Warning deleted',
            null,
            $meta,
        );

        return redirect()->back()->with('message', 'Warning deleted.');
    }

    // ==================================================================
    //  COMPLAINTS
    // ==================================================================

    public function storeComplaint(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'reporter_user_id' => 'nullable|exists:users,id',
            'reporter_name'    => 'nullable|string|max:255',
            'reporter_email'   => 'nullable|email|max:255',
            'reporter_phone'   => 'nullable|string|max:50',
            'booking_id'       => 'nullable|integer',
            'category'         => 'required|in:harassment,safety,misconduct,no_show,pricing_dispute,vehicle_condition,late,unprofessional,inappropriate_contact,other',
            'severity'         => 'required|in:low,medium,high,critical',
            'subject'          => 'required|string|max:255',
            'description'      => 'required|string|max:10000',
        ]);

        $complaint = InstructorComplaint::create([
            'instructor_profile_id' => $instructorProfile->id,
            'reporter_user_id'      => $request->input('reporter_user_id'),
            'reporter_name'         => $request->input('reporter_name'),
            'reporter_email'        => $request->input('reporter_email'),
            'reporter_phone'        => $request->input('reporter_phone'),
            'booking_id'            => $request->input('booking_id'),
            'category'              => $request->input('category'),
            'severity'              => $request->input('severity'),
            'subject'               => $request->input('subject'),
            'description'           => $request->input('description'),
            'status'                => InstructorComplaint::STATUS_OPEN,
            'created_by'            => Auth::id(),
        ]);

        InstructorAuditLog::record(
            $instructorProfile->id,
            Auth::id(),
            'complaint_added',
            ucfirst($request->input('severity')) . ' complaint: ' . $request->input('subject'),
            null,
            ['complaint_id' => $complaint->id, 'category' => $request->input('category'), 'severity' => $request->input('severity')],
        );

        return redirect()->back()->with('message', 'Complaint recorded.');
    }

    public function updateComplaintStatus(Request $request, InstructorComplaint $instructorComplaint)
    {
        $request->validate([
            'status'            => 'required|in:open,investigating,resolved,dismissed,escalated',
            'resolution_notes'  => 'nullable|string|max:5000',
            'police_reported'   => 'nullable|boolean',
            'police_reference'  => 'nullable|string|max:255',
        ]);

        $old = ['status' => $instructorComplaint->status];
        $data = [
            'status'            => $request->input('status'),
            'resolution_notes'  => $request->input('resolution_notes'),
            'police_reported'   => (bool) $request->input('police_reported', false),
            'police_reference'  => $request->input('police_reference'),
        ];

        if (in_array($request->input('status'), ['resolved', 'dismissed'])) {
            $data['resolved_by'] = Auth::id();
            $data['resolved_at'] = now();
        }

        if ($request->input('police_reported') && ! $instructorComplaint->police_reported_at) {
            $data['police_reported_at'] = now();
        }

        $instructorComplaint->update($data);

        InstructorAuditLog::record(
            $instructorComplaint->instructor_profile_id,
            Auth::id(),
            'complaint_updated',
            'Complaint status → ' . $request->input('status'),
            $old,
            ['complaint_id' => $instructorComplaint->id, 'status' => $request->input('status')],
        );

        return redirect()->back()->with('message', 'Complaint updated.');
    }

    public function deleteComplaint(InstructorComplaint $instructorComplaint)
    {
        $profileId = $instructorComplaint->instructor_profile_id;
        $meta = ['complaint_id' => $instructorComplaint->id, 'subject' => $instructorComplaint->subject];
        $instructorComplaint->delete();

        InstructorAuditLog::record(
            $profileId,
            Auth::id(),
            'complaint_deleted',
            'Complaint deleted',
            null,
            $meta,
        );

        return redirect()->back()->with('message', 'Complaint deleted.');
    }

    // ==================================================================
    //  ADMIN NOTES
    // ==================================================================

    public function storeNote(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'note'   => 'required|string|max:5000',
            'pinned' => 'nullable|boolean',
        ]);

        $note = InstructorAdminNote::create([
            'instructor_profile_id' => $instructorProfile->id,
            'admin_id'              => Auth::id(),
            'note'                  => $request->input('note'),
            'pinned'                => (bool) $request->input('pinned', false),
        ]);

        InstructorAuditLog::record(
            $instructorProfile->id,
            Auth::id(),
            'note_added',
            'Added internal note',
            null,
            ['note_id' => $note->id, 'pinned' => $note->pinned],
        );

        return redirect()->back()->with('message', 'Note added.');
    }

    public function deleteNote(InstructorAdminNote $instructorAdminNote)
    {
        $profileId = $instructorAdminNote->instructor_profile_id;
        $instructorAdminNote->delete();

        InstructorAuditLog::record(
            $profileId,
            Auth::id(),
            'note_deleted',
            'Deleted internal note',
            null,
            null,
        );

        return redirect()->back()->with('message', 'Note deleted.');
    }

    public function toggleNotePin(InstructorAdminNote $instructorAdminNote)
    {
        $instructorAdminNote->pinned = ! $instructorAdminNote->pinned;
        $instructorAdminNote->save();

        return redirect()->back()->with('message', $instructorAdminNote->pinned ? 'Note pinned.' : 'Note unpinned.');
    }

    // ==================================================================
    //  CORRESPONDENCE LOG
    // ==================================================================

    public function storeCorrespondence(Request $request, InstructorProfile $instructorProfile)
    {
        $request->validate([
            'channel'              => 'required|in:email,sms,phone_call,in_person,system_message,other',
            'direction'            => 'required|in:outbound,inbound',
            'subject'              => 'nullable|string|max:255',
            'body'                 => 'required|string|max:20000',
            'communicated_at'      => 'nullable|date',
            'related_complaint_id' => 'nullable|exists:instructor_complaints,id',
            'related_warning_id'   => 'nullable|exists:instructor_warnings,id',
            'related_block_id'     => 'nullable|exists:instructor_blocks,id',
        ]);

        $entry = InstructorCorrespondence::create([
            'instructor_profile_id' => $instructorProfile->id,
            'admin_id'              => Auth::id(),
            'channel'               => $request->input('channel'),
            'direction'             => $request->input('direction'),
            'subject'               => $request->input('subject'),
            'body'                  => $request->input('body'),
            'communicated_at'       => $request->input('communicated_at') ?: now(),
            'related_complaint_id'  => $request->input('related_complaint_id'),
            'related_warning_id'    => $request->input('related_warning_id'),
            'related_block_id'      => $request->input('related_block_id'),
        ]);

        InstructorAuditLog::record(
            $instructorProfile->id,
            Auth::id(),
            'correspondence_logged',
            ucfirst($request->input('direction')) . ' ' . $request->input('channel') . ($request->input('subject') ? ': ' . $request->input('subject') : ''),
            null,
            ['correspondence_id' => $entry->id],
        );

        return redirect()->back()->with('message', 'Correspondence logged.');
    }

    public function deleteCorrespondence(InstructorCorrespondence $instructorCorrespondence)
    {
        $profileId = $instructorCorrespondence->instructor_profile_id;
        $instructorCorrespondence->delete();

        InstructorAuditLog::record(
            $profileId,
            Auth::id(),
            'correspondence_deleted',
            'Correspondence entry deleted',
            null,
            null,
        );

        return redirect()->back()->with('message', 'Correspondence deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\AdminBookingAlert;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingProposed;
use App\Notifications\InstructorArrived;
use App\Notifications\InstructorNewBooking;
use App\Notifications\LessonConfirmationRequest;
use App\Notifications\ReviewRequested;
use App\Services\BookingAvailabilityService;
use App\Services\GoogleCalendarService;
use App\Services\RatingService;
use App\Traits\NotifiesAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    use NotifiesAdmin;

    public function __construct(
        protected BookingAvailabilityService $availabilityService
    ) {}

    /**
     * List bookings for the authenticated user (learner or instructor).
     * For instructors, optional ?tab=upcoming|pending|history to filter by dashboard tab.
     *
     * Calendar mode (?calendar=1):
     *   Returns ALL non-cancelled bookings within a date range (not paginated).
     *   Supports &from=YYYY-MM-DD&to=YYYY-MM-DD (defaults to ±60 days).
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = $user->isLearner()
            ? Booking::where('learner_id', $user->id)
            : Booking::where('instructor_id', $user->id);

        // --- Calendar mode: return all bookings in a date window (no pagination) ---
        if ($request->boolean('calendar')) {
            $from = $request->input('from')
                ? \Carbon\Carbon::parse($request->input('from'))->startOfDay()
                : now()->subDays(60)->startOfDay();
            $to = $request->input('to')
                ? \Carbon\Carbon::parse($request->input('to'))->endOfDay()
                : now()->addDays(60)->endOfDay();

            $bookings = $query
                ->where('status', '!=', Booking::STATUS_CANCELLED)
                ->whereBetween('scheduled_at', [$from, $to])
                ->orderBy('scheduled_at', 'asc')
                ->with(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state'])
                ->get()
                ->map(fn (Booking $b) => $this->formatBooking($b));

            return response()->json(['data' => $bookings]);
        }

        // --- Normal paginated mode ---
        $tab = $request->input('tab');
        $now = now();
        if ($user->isInstructor() && in_array($tab, ['upcoming', 'pending', 'history'], true)) {
            if ($tab === 'upcoming') {
                $query->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PROPOSED])
                    ->where('scheduled_at', '>', $now)
                    ->orderBy('scheduled_at', 'asc');
            } elseif ($tab === 'pending') {
                $query->where('status', Booking::STATUS_PROPOSED)
                    ->orderBy('scheduled_at', 'asc');
            } else {
                $query->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED])
                    ->orderBy('scheduled_at', 'desc');
            }
        } elseif ($user->isLearner() && in_array($tab, ['upcoming', 'pending', 'history'], true)) {
            if ($tab === 'upcoming') {
                // Confirmed or in-progress bookings scheduled for the future (or actively running)
                $query->whereIn('status', [
                        Booking::STATUS_CONFIRMED,
                        Booking::STATUS_PROPOSED,
                        Booking::STATUS_INSTRUCTOR_ARRIVED,
                        Booking::STATUS_IN_PROGRESS,
                    ])
                    ->where(function ($q) use ($now) {
                        $q->where('scheduled_at', '>', $now)
                          ->orWhereIn('status', [Booking::STATUS_INSTRUCTOR_ARRIVED, Booking::STATUS_IN_PROGRESS]);
                    })
                    ->orderBy('scheduled_at', 'asc');
            } elseif ($tab === 'pending') {
                // Learner's pending bookings — awaiting payment/confirmation
                $query->where('status', Booking::STATUS_PENDING)
                    ->orderBy('scheduled_at', 'asc');
            } else {
                $query->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED])
                    ->orderBy('scheduled_at', 'desc');
            }
        } else {
            $status = $request->input('status');
            if (in_array($status, [Booking::STATUS_PENDING, Booking::STATUS_PROPOSED, Booking::STATUS_CONFIRMED, Booking::STATUS_INSTRUCTOR_ARRIVED, Booking::STATUS_IN_PROGRESS, Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED], true)) {
                $query->where('status', $status);
            }
            $query->orderBy('scheduled_at', 'desc');
        }

        $bookings = $query->with(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state', 'review'])
            ->paginate(20);

        $items = $bookings->getCollection()->map(fn (Booking $b) => $this->formatBooking($b));
        $bookings->setCollection($items);

        return response()->json($bookings);
    }

    /**
     * Create a new booking (learner only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'instructor_profile_id' => ['required', 'exists:instructor_profiles,id'],
            'suburb_id' => ['nullable', 'exists:suburbs,id'],
            'type' => ['required', Rule::in([Booking::TYPE_LESSON, Booking::TYPE_TEST_PACKAGE])],
            'transmission' => ['required', Rule::in(['auto', 'manual'])],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'learner_notes' => ['nullable', 'string', 'max:1000'],
            'test_pre_booked' => ['boolean'],
        ]);

        $user = Auth::user();
        if (! $user->isLearner()) {
            return response()->json(['message' => 'Only learners can create bookings.'], 403);
        }

        $profile = InstructorProfile::findOrFail($request->input('instructor_profile_id'));
        if (! $profile->is_active) {
            return response()->json(['message' => 'Instructor is not available.'], 422);
        }

        $scheduledAt = $request->date('scheduled_at');
        $slots = $this->availabilityService->getAvailableSlots($profile, $scheduledAt->format('Y-m-d'));
        $timeKey = $scheduledAt->format('H:i');
        $allowed = collect($slots)->pluck('time')->contains($timeKey);
        if (! $allowed) {
            return response()->json(['message' => 'Selected time is not available.'], 422);
        }

        $amount = $request->input('type') === Booking::TYPE_TEST_PACKAGE
            ? $profile->test_package_price
            : $profile->lesson_price;
        $duration = $profile->lesson_duration_minutes ?: 60;

        $booking = DB::transaction(function () use ($request, $user, $profile, $scheduledAt, $amount, $duration) {
            $bookingAmount = $amount ?? 0;
            $feePercent = (float) SiteSetting::get('platform_fee_percent', 4);
            $platformFee = round($bookingAmount * $feePercent / 100, 2);
            $serviceFee = (float) SiteSetting::get('platform_service_fee', 5.00);
            $processingFee = (float) SiteSetting::get('payment_processing_fee', 2.00);
            $instructorNet = max(round($bookingAmount - $serviceFee - $processingFee, 2), 0);

            return Booking::create([
                'learner_id' => $user->id,
                'instructor_id' => $profile->user_id,
                'instructor_profile_id' => $profile->id,
                'suburb_id' => $request->input('suburb_id'),
                'type' => $request->input('type'),
                'transmission' => $request->input('transmission'),
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'amount' => $bookingAmount,
                'platform_fee' => $platformFee,
                'instructor_net_amount' => $instructorNet,
                'test_pre_booked' => $request->boolean('test_pre_booked'),
                'status' => Booking::STATUS_CONFIRMED,
                'learner_notes' => $request->input('learner_notes'),
            ]);
        });

        $booking->load(['instructor:id,name', 'suburb']);

        // Notify the instructor about the new booking
        try {
            $instructor = User::find($booking->instructor_id);
            if ($instructor) {
                $instructor->notify(new InstructorNewBooking($booking));
            }
        } catch (\Throwable $e) {
            Log::warning('Instructor booking notification failed: ' . $e->getMessage());
        }

        // Notify admin about the new booking
        $this->notifyAdminAboutBooking($booking, AdminBookingAlert::EVENT_NEW);

        // Push to Google Calendar for both parties if connected
        $this->syncBookingToGoogleCalendar($booking);

        return response()->json(['data' => $this->formatBooking($booking)], 201);
    }

    /**
     * Cancel a booking.
     *
     * Live site flow:
     * - Instructor sees preset reason dropdown + message for learner + cancellation policy checkbox
     * - 24-hour restriction for instructors (only emergency reasons allowed)
     * - Cancellation counts towards instructor's cancellation rate
     * - Other party is notified with the reason and message
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $validReasonCodes = array_keys(Booking::cancellationReasonLabels());

        $request->validate([
            'cancellation_reason_code' => ['required', Rule::in($validReasonCodes)],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
            'cancellation_message' => ['nullable', 'string', 'max:1000'],
            'cancellation_policy_accepted' => ['required', 'accepted'],
        ]);

        $user = Auth::user();

        // Must be a party to this booking
        if ($user->id !== $booking->learner_id && $user->id !== $booking->instructor_id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Must be in a cancellable state
        if (! $booking->isCancellable()) {
            return response()->json(['message' => 'Booking cannot be cancelled.'], 422);
        }

        $reasonCode = $request->input('cancellation_reason_code');

        // 24-hour restriction for instructors
        if ($user->id === $booking->instructor_id && $booking->isWithinModificationCutoff()) {
            if (! $booking->canUserModify($user, $reasonCode)) {
                return response()->json([
                    'message' => 'This booking starts within 24 hours. Instructors are not permitted to cancel bookings within 24 hours unless it is an emergency (illness/family emergency or car trouble).',
                    'restriction' => '24_hour_cutoff',
                ], 422);
            }
        }

        // Get human-readable reason label
        $reasonLabel = Booking::cancellationReasonLabels()[$reasonCode] ?? $reasonCode;
        $freeTextReason = $request->input('cancellation_reason');
        $fullReason = $reasonCode === Booking::CANCEL_REASON_OTHER && $freeTextReason
            ? $freeTextReason
            : $reasonLabel;

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancellation_reason' => $fullReason,
            'cancellation_reason_code' => $reasonCode,
            'cancellation_message' => $request->input('cancellation_message'),
            'cancelled_at' => now(),
            'cancelled_by_id' => $user->id,
            'cancellation_policy_accepted' => true,
        ]);

        // Notify the other party about cancellation
        try {
            $otherUserId = ($user->id === $booking->learner_id)
                ? $booking->instructor_id
                : $booking->learner_id;
            $otherUser = User::find($otherUserId);
            if ($otherUser) {
                $otherUser->notify(new BookingCancelled($booking, $fullReason));
            }
        } catch (\Throwable $e) {
            Log::warning('Cancel notification failed: ' . $e->getMessage());
        }

        // Notify admin about the cancellation
        $this->notifyAdminAboutBooking($booking, AdminBookingAlert::EVENT_CANCELLED, $fullReason);

        // Remove from Google Calendar for both parties
        $this->deleteBookingFromGoogleCalendar($booking);

        // Calculate cancellation rate for instructor context
        $cancellationRate = null;
        if ($user->isInstructor()) {
            $cancellationRate = Booking::cancellationRateForUser($user->id);
        }

        return response()->json([
            'data' => $this->formatBooking($booking->fresh()),
            'cancellation_rate' => $cancellationRate,
        ]);
    }

    /**
     * Reschedule a booking.
     *
     * Live site flow:
     * - Reschedule = CANCEL the current booking + PROPOSE a NEW booking
     * - The original booking gets cancelled with a system reason
     * - A new booking is created with status PROPOSED
     * - The learner is notified and can accept or decline the new proposal
     * - 24-hour restriction applies for instructors
     */
    public function reschedule(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'cancellation_reason_code' => ['nullable', Rule::in(array_keys(Booking::cancellationReasonLabels()))],
            'cancellation_message' => ['nullable', 'string', 'max:1000'],
            'cancellation_policy_accepted' => ['required', 'accepted'],
        ]);

        $user = Auth::user();

        // Must be a party to this booking
        if ($user->id !== $booking->learner_id && $user->id !== $booking->instructor_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (! $booking->isReschedulable()) {
            return response()->json(['message' => 'Booking cannot be rescheduled.'], 422);
        }

        $reasonCode = $request->input('cancellation_reason_code');

        // 24-hour restriction for instructors
        if ($user->id === $booking->instructor_id && $booking->isWithinModificationCutoff()) {
            if (! $booking->canUserModify($user, $reasonCode)) {
                return response()->json([
                    'message' => 'This booking starts within 24 hours. Instructors are not permitted to modify bookings within 24 hours unless it is an emergency.',
                    'restriction' => '24_hour_cutoff',
                ], 422);
            }
        }

        // Verify new time slot is available
        $profile = $booking->instructor->instructorProfile ?? InstructorProfile::where('user_id', $booking->instructor_id)->first();
        if (! $profile) {
            return response()->json(['message' => 'Instructor profile not found.'], 422);
        }

        $scheduledAt = $request->date('scheduled_at');
        $slots = $this->availabilityService->getAvailableSlots($profile, $scheduledAt->format('Y-m-d'));
        $timeKey = $scheduledAt->format('H:i');
        $allowed = collect($slots)->pluck('time')->contains($timeKey);
        if (! $allowed) {
            return response()->json(['message' => 'Selected time is not available.'], 422);
        }

        return DB::transaction(function () use ($booking, $user, $scheduledAt, $request, $reasonCode, $profile) {
            // Step 1: Cancel the original booking
            $reasonLabel = $reasonCode
                ? (Booking::cancellationReasonLabels()[$reasonCode] ?? $reasonCode)
                : 'Rescheduled to new time';

            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'cancellation_reason' => 'Rescheduled: ' . $reasonLabel,
                'cancellation_reason_code' => $reasonCode ?? 'rescheduled',
                'cancellation_message' => $request->input('cancellation_message'),
                'cancelled_at' => now(),
                'cancelled_by_id' => $user->id,
                'cancellation_policy_accepted' => true,
            ]);

            // Step 2: Create a new PROPOSED booking linked to original
            $newBooking = Booking::create([
                'learner_id' => $booking->learner_id,
                'instructor_id' => $booking->instructor_id,
                'instructor_profile_id' => $profile->id,
                'suburb_id' => $booking->suburb_id,
                'type' => $booking->type,
                'transmission' => $booking->transmission,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $booking->duration_minutes,
                'amount' => $booking->amount,
                'platform_fee' => $booking->platform_fee,
                'test_pre_booked' => $booking->test_pre_booked,
                'status' => Booking::STATUS_PROPOSED,
                'learner_notes' => $booking->learner_notes,
                'proposal_expires_at' => min(
                    now()->addHours(24),
                    $scheduledAt
                ),
                'rescheduled_from_booking_id' => $booking->id,
            ]);

            // Step 3: Notify learner about the reschedule
            try {
                $learner = User::find($booking->learner_id);
                if ($learner) {
                    $learner->notify(new BookingCancelled($booking, 'Rescheduled: ' . $reasonLabel));
                    // Also notify about the new proposed booking
                    if (class_exists(\App\Notifications\BookingProposed::class)) {
                        $learner->notify(new BookingProposed($newBooking));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Reschedule notification failed: ' . $e->getMessage());
            }

            $newBooking->load(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state']);

            // Google Calendar: remove old event, push new one
            $this->deleteBookingFromGoogleCalendar($booking);
            $this->syncBookingToGoogleCalendar($newBooking);

            return response()->json([
                'message' => 'Booking rescheduled. The learner has been notified and can accept or decline the new booking.',
                'data' => [
                    'cancelled_booking' => $this->formatBooking($booking->fresh()),
                    'new_booking' => $this->formatBooking($newBooking),
                ],
            ]);
        });
    }

    /**
     * Show single booking with modification context.
     */
    public function show(Booking $booking): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $booking->learner_id && $user->id !== $booking->instructor_id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $booking->load(['learner', 'instructor', 'suburb.state', 'review', 'cancelledBy', 'rescheduledFromBooking', 'rescheduledToBooking']);

        $data = $this->formatBooking($booking);

        // Add modification context for the UI
        $data['can_cancel'] = $booking->isCancellable();
        $data['can_reschedule'] = $booking->isReschedulable();
        $data['is_within_24_hours'] = $booking->isWithinModificationCutoff();
        $data['cancellation_reason_options'] = Booking::cancellationReasonLabels();

        // If instructor, add cancellation rate
        if ($user->isInstructor()) {
            $data['instructor_cancellation_rate'] = Booking::cancellationRateForUser($user->id);
        }

        // If this booking was rescheduled, include link
        if ($booking->rescheduledToBooking) {
            $data['rescheduled_to_booking_id'] = $booking->rescheduledToBooking->id;
        }
        if ($booking->rescheduledFromBooking) {
            $data['rescheduled_from_booking_id'] = $booking->rescheduledFromBooking->id;
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get cancellation reason options and booking modification context.
     * Used by the frontend to populate the cancel/reschedule modals.
     */
    public function modificationContext(Booking $booking): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $booking->learner_id && $user->id !== $booking->instructor_id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $isInstructor = $user->id === $booking->instructor_id;
        $withinCutoff = $booking->isWithinModificationCutoff();

        return response()->json([
            'can_cancel' => $booking->isCancellable(),
            'can_reschedule' => $booking->isReschedulable(),
            'is_within_24_hours' => $withinCutoff,
            'cancellation_reason_options' => Booking::cancellationReasonLabels(),
            'restriction_message' => ($isInstructor && $withinCutoff)
                ? 'This booking starts within 24 hours. Instructors are not permitted to modify bookings within 24 hours unless it is an emergency.'
                : null,
            'cancellation_rate_warning' => $isInstructor
                ? 'This may count towards your cancellation rate. We measure your cancellation rate to ensure that learners enjoy a consistent experience on our platform.'
                : null,
            'cancellation_rate' => $isInstructor
                ? Booking::cancellationRateForUser($user->id)
                : null,
        ]);
    }

    /**
     * Mark a booking as completed (instructor only) — direct shortcut.
     *
     * NOTE: The preferred flow is now markArrived() -> startLesson() -> endLesson().
     * This method remains as a direct shortcut for cases where the full tracking flow
     * was not used (e.g. instructor marks complete after the fact).
     */
    public function complete(Booking $booking): JsonResponse
    {
        $user = Auth::user();

        // Only the instructor of this booking can mark it complete
        if ($user->id !== $booking->instructor_id) {
            return response()->json(['message' => 'Only the instructor can mark a booking as completed.'], 403);
        }

        // Allow completion from confirmed, instructor_arrived, or in_progress status
        $allowedStatuses = [
            Booking::STATUS_CONFIRMED,
            Booking::STATUS_INSTRUCTOR_ARRIVED,
            Booking::STATUS_IN_PROGRESS,
        ];
        if (!in_array($booking->status, $allowedStatuses, true)) {
            return response()->json(['message' => 'Only confirmed or in-progress bookings can be marked as completed.'], 422);
        }

        if ($booking->status === Booking::STATUS_CONFIRMED && $booking->scheduled_at->isFuture()) {
            return response()->json(['message' => 'Cannot complete a booking before its scheduled time.'], 422);
        }

        $updateData = ['status' => Booking::STATUS_COMPLETED];

        // If ending from in_progress and no lesson_ended_at, record it now
        if ($booking->status === Booking::STATUS_IN_PROGRESS && !$booking->lesson_ended_at) {
            $updateData['lesson_ended_at'] = now();
        }

        $booking->update($updateData);

        // Update instructor's weighted rating for the completed lesson
        try {
            $instructorProfile = InstructorProfile::where('user_id', $booking->instructor_id)->first();
            if ($instructorProfile) {
                app(RatingService::class)->recordLessonCompleted($instructorProfile);
            }
        } catch (\Throwable $e) {
            Log::warning('Rating update failed after lesson completion: ' . $e->getMessage());
        }

        // Generate confirmation token and send confirmation request to learner
        try {
            $learner = User::find($booking->learner_id);
            if ($learner) {
                $booking->load('instructor');

                // Generate unique confirmation token
                $booking->generateConfirmationToken();

                // Send lesson confirmation request (email + SMS)
                $learner->notify(new LessonConfirmationRequest($booking));

                // Also send review request
                $learner->notify(new ReviewRequested($booking));
            }
        } catch (\Throwable $e) {
            Log::warning('Lesson confirmation/review notification failed: ' . $e->getMessage());
        }

        // Notify admin about the completion
        $this->notifyAdminAboutBooking($booking, AdminBookingAlert::EVENT_COMPLETED);

        // Update Google Calendar events with completed status
        $this->updateBookingOnGoogleCalendar($booking);

        return response()->json([
            'data' => $this->formatBooking($booking->fresh()),
            'message' => 'Booking marked as completed. The learner has been asked to confirm the lesson.',
        ]);
    }

    /**
     * Mark that the instructor has arrived at the lesson location.
     * Transitions: confirmed -> instructor_arrived
     */
    public function markArrived(Request $request, Booking $booking): JsonResponse
    {
        $user = Auth::user();

        // Only the instructor of this booking can mark arrived
        if ($user->id !== $booking->instructor_id) {
            return response()->json(['message' => 'Only the instructor can mark arrival.'], 403);
        }

        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return response()->json(['message' => 'Only confirmed bookings can be marked as arrived.'], 422);
        }

        $booking->update([
            'status' => Booking::STATUS_INSTRUCTOR_ARRIVED,
            'instructor_arrived_at' => now(),
        ]);

        // Notify learner that instructor has arrived
        try {
            $learner = User::find($booking->learner_id);
            if ($learner) {
                $booking->load('instructor');
                $learner->notify(new InstructorArrived($booking));
            }
        } catch (\Throwable $e) {
            Log::warning('Instructor arrived notification failed: ' . $e->getMessage());
        }

        // Update Google Calendar events with arrived status
        $this->updateBookingOnGoogleCalendar($booking);

        return response()->json([
            'data' => $this->formatBooking($booking->fresh()),
            'message' => 'Instructor arrival recorded. The learner has been notified.',
        ]);
    }

    /**
     * Start the lesson.
     * Transitions: instructor_arrived -> in_progress
     */
    public function startLesson(Request $request, Booking $booking): JsonResponse
    {
        $user = Auth::user();

        // Only the instructor of this booking can start the lesson
        if ($user->id !== $booking->instructor_id) {
            return response()->json(['message' => 'Only the instructor can start the lesson.'], 403);
        }

        if ($booking->status !== Booking::STATUS_INSTRUCTOR_ARRIVED) {
            return response()->json(['message' => 'Instructor must be marked as arrived before starting the lesson.'], 422);
        }

        $booking->update([
            'status' => Booking::STATUS_IN_PROGRESS,
            'lesson_started_at' => now(),
        ]);

        // Update Google Calendar events with in-progress status
        $this->updateBookingOnGoogleCalendar($booking);

        return response()->json([
            'data' => $this->formatBooking($booking->fresh()),
            'message' => 'Lesson started.',
        ]);
    }

    /**
     * End the lesson and mark booking as completed.
     * Transitions: in_progress -> completed
     * This is the preferred completion flow (vs. the direct complete() shortcut).
     */
    public function endLesson(Request $request, Booking $booking): JsonResponse
    {
        $user = Auth::user();

        // Only the instructor of this booking can end the lesson
        if ($user->id !== $booking->instructor_id) {
            return response()->json(['message' => 'Only the instructor can end the lesson.'], 403);
        }

        if ($booking->status !== Booking::STATUS_IN_PROGRESS) {
            return response()->json(['message' => 'Only in-progress lessons can be ended.'], 422);
        }

        $booking->update([
            'status' => Booking::STATUS_COMPLETED,
            'lesson_ended_at' => now(),
        ]);

        // Update instructor's weighted rating for the completed lesson
        try {
            $instructorProfile = InstructorProfile::where('user_id', $booking->instructor_id)->first();
            if ($instructorProfile) {
                app(RatingService::class)->recordLessonCompleted($instructorProfile);
            }
        } catch (\Throwable $e) {
            Log::warning('Rating update failed after lesson end: ' . $e->getMessage());
        }

        // Generate confirmation token and send notifications (same as complete())
        try {
            $learner = User::find($booking->learner_id);
            if ($learner) {
                $booking->load('instructor');

                // Generate unique confirmation token
                $booking->generateConfirmationToken();

                // Send lesson confirmation request (email + SMS)
                $learner->notify(new LessonConfirmationRequest($booking));

                // Also send review request
                $learner->notify(new ReviewRequested($booking));
            }
        } catch (\Throwable $e) {
            Log::warning('End lesson notification failed: ' . $e->getMessage());
        }

        // Notify admin about the completion
        $this->notifyAdminAboutBooking($booking, AdminBookingAlert::EVENT_COMPLETED);

        // Update Google Calendar events with completed status
        $this->updateBookingOnGoogleCalendar($booking);

        $freshBooking = $booking->fresh();

        return response()->json([
            'data' => $this->formatBooking($freshBooking),
            'actual_duration_minutes' => $freshBooking->actualDurationMinutes(),
            'message' => 'Lesson ended and booking completed. The learner has been asked to confirm.',
        ]);
    }

    private function formatBooking(Booking $b): array
    {
        $b->loadMissing(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state']);
        $location = null;
        if ($b->suburb) {
            $parts = array_filter([$b->suburb->name, $b->suburb->postcode, $b->suburb->state?->code ?? $b->suburb->state?->name ?? null]);
            $location = implode(' ', $parts);
        }

        // Only expose learner contact info to the instructor of this booking or admin
        $authUser = auth()->user();
        $canSeeContact = $authUser && (
            $authUser->id === $b->instructor_id ||
            $authUser->role === 'admin' ||
            $authUser->id === $b->learner_id
        );

        $learnerData = $b->learner ? ['id' => $b->learner->id, 'name' => $b->learner->name] : null;
        if ($canSeeContact && $b->learner) {
            $learnerData['email'] = $b->learner->email;
            $learnerData['phone'] = $b->learner->phone;
        }

        return [
            'id' => $b->id,
            'learner' => $learnerData,
            'instructor' => $b->instructor ? ['id' => $b->instructor->id, 'name' => $b->instructor->name] : null,
            'suburb' => $b->suburb ? ['id' => $b->suburb->id, 'name' => $b->suburb->name, 'postcode' => $b->suburb->postcode, 'state_code' => $b->suburb->state?->code, 'location' => $location] : null,
            'type' => $b->type,
            'transmission' => $b->transmission,
            'scheduled_at' => $b->scheduled_at->toIso8601String(),
            'duration_minutes' => $b->duration_minutes,
            'amount' => (float) $b->amount,
            'platform_fee' => (float) ($b->platform_fee ?? 0),
            'instructor_net_amount' => (float) ($b->instructor_net_amount ?? 0),
            'instructor_payout_id' => $b->instructor_payout_id,
            'test_pre_booked' => $b->test_pre_booked,
            'status' => $b->status,
            'payment_method' => $b->payment_method,
            'payment_status' => $b->payment_status ?? ($b->status === Booking::STATUS_COMPLETED ? 'paid' : ($b->status === Booking::STATUS_CANCELLED ? 'refunded' : 'pending')),
            'learner_notes' => $b->learner_notes,
            'cancellation_reason' => $b->cancellation_reason,
            'cancellation_reason_code' => $b->cancellation_reason_code,
            'cancellation_message' => $b->cancellation_message,
            'cancelled_at' => $b->cancelled_at?->toIso8601String(),
            'cancelled_by_id' => $b->cancelled_by_id,
            'rescheduled_from_booking_id' => $b->rescheduled_from_booking_id,
            'instructor_arrived_at' => $b->instructor_arrived_at?->toIso8601String(),
            'lesson_started_at' => $b->lesson_started_at?->toIso8601String(),
            'lesson_ended_at' => $b->lesson_ended_at?->toIso8601String(),
            'actual_duration_minutes' => $b->actualDurationMinutes(),
            'learner_confirmed' => $b->learner_confirmed_at !== null,
            'learner_confirmed_at' => $b->learner_confirmed_at?->toIso8601String(),
            'confirmation_pending' => $b->isConfirmationPending(),
            'review' => $b->relationLoaded('review') && $b->review
                ? ['id' => $b->review->id, 'rating' => $b->review->rating, 'comment' => $b->review->comment]
                : null,
        ];
    }

    /**
     * Push a new booking to Google Calendar for both the instructor and learner (if connected).
     * Failures are logged but never block the booking flow.
     */
    private function syncBookingToGoogleCalendar(Booking $booking): void
    {
        try {
            $gcal = app(GoogleCalendarService::class);

            // Instructor's calendar
            $instructor = User::find($booking->instructor_id);
            if ($instructor && $instructor->isGoogleCalendarConnected()) {
                $eventId = $gcal->pushBookingToCalendar($instructor, $booking);
                if ($eventId) {
                    $booking->update(['google_event_id' => $eventId]);
                }
            }

            // Learner's calendar
            $learner = User::find($booking->learner_id);
            if ($learner && $learner->isGoogleCalendarConnected()) {
                $eventId = $gcal->pushBookingToCalendar($learner, $booking);
                if ($eventId) {
                    $booking->update(['google_event_id_learner' => $eventId]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Google Calendar sync failed for booking ' . $booking->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Update the Google Calendar events for a booking (both instructor and learner).
     * Called on status changes like arrived, start, end, complete.
     */
    private function updateBookingOnGoogleCalendar(Booking $booking): void
    {
        try {
            $gcal = app(GoogleCalendarService::class);

            // Instructor's calendar
            if ($booking->google_event_id) {
                $instructor = User::find($booking->instructor_id);
                if ($instructor && $instructor->isGoogleCalendarConnected()) {
                    $gcal->updateCalendarEvent($instructor, $booking, $booking->google_event_id);
                }
            }

            // Learner's calendar
            if ($booking->google_event_id_learner) {
                $learner = User::find($booking->learner_id);
                if ($learner && $learner->isGoogleCalendarConnected()) {
                    $gcal->updateCalendarEvent($learner, $booking, $booking->google_event_id_learner);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Google Calendar update failed for booking ' . $booking->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete Google Calendar events for a booking (both instructor and learner).
     * Called on cancellation.
     */
    private function deleteBookingFromGoogleCalendar(Booking $booking): void
    {
        try {
            $gcal = app(GoogleCalendarService::class);

            // Instructor's calendar
            if ($booking->google_event_id) {
                $instructor = User::find($booking->instructor_id);
                if ($instructor && $instructor->isGoogleCalendarConnected()) {
                    $gcal->deleteCalendarEvent($instructor, $booking->google_event_id);
                }
                $booking->update(['google_event_id' => null]);
            }

            // Learner's calendar
            if ($booking->google_event_id_learner) {
                $learner = User::find($booking->learner_id);
                if ($learner && $learner->isGoogleCalendarConnected()) {
                    $gcal->deleteCalendarEvent($learner, $booking->google_event_id_learner);
                }
                $booking->update(['google_event_id_learner' => null]);
            }
        } catch (\Throwable $e) {
            Log::warning('Google Calendar delete failed for booking ' . $booking->id . ': ' . $e->getMessage());
        }
    }
}

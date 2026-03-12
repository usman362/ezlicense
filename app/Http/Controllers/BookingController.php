<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Services\BookingAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function __construct(
        protected BookingAvailabilityService $availabilityService
    ) {}

    /**
     * List bookings for the authenticated user (learner or instructor).
     * For instructors, optional ?tab=upcoming|pending|history to filter by dashboard tab.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = $user->isLearner()
            ? Booking::where('learner_id', $user->id)
            : Booking::where('instructor_id', $user->id);

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
        } elseif ($user->isLearner() && $tab === 'history') {
            $query->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED])
                ->orderBy('scheduled_at', 'desc');
        } else {
            $status = $request->input('status');
            if (in_array($status, [Booking::STATUS_PENDING, Booking::STATUS_PROPOSED, Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED], true)) {
                $query->where('status', $status);
            }
            $query->orderBy('scheduled_at', 'desc');
        }

        $bookings = $query->with(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state'])
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
            return Booking::create([
                'learner_id' => $user->id,
                'instructor_id' => $profile->user_id,
                'suburb_id' => $request->input('suburb_id'),
                'type' => $request->input('type'),
                'transmission' => $request->input('transmission'),
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'amount' => $amount ?? 0,
                'test_pre_booked' => $request->boolean('test_pre_booked'),
                'status' => Booking::STATUS_CONFIRMED,
                'learner_notes' => $request->input('learner_notes'),
            ]);
        });

        $booking->load(['instructor:id,name', 'suburb']);

        return response()->json(['data' => $this->formatBooking($booking)], 201);
    }

    /**
     * Reschedule a booking (learner, within policy e.g. 24h before).
     */
    public function reschedule(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $user = Auth::user();
        $canReschedule = ($user->id === $booking->learner_id || $user->id === $booking->instructor_id)
            && $booking->isReschedulable();

        if (! $canReschedule) {
            return response()->json(['message' => 'Booking cannot be rescheduled.'], 422);
        }

        $profile = $booking->instructor->instructorProfile;
        $scheduledAt = $request->date('scheduled_at');
        $slots = $this->availabilityService->getAvailableSlots($profile, $scheduledAt->format('Y-m-d'));
        $timeKey = $scheduledAt->format('H:i');
        $allowed = collect($slots)->pluck('time')->contains($timeKey);
        if (! $allowed) {
            return response()->json(['message' => 'Selected time is not available.'], 422);
        }

        $booking->update(['scheduled_at' => $scheduledAt]);

        return response()->json(['data' => $this->formatBooking($booking->fresh())]);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $canCancel = ($user->id === $booking->learner_id || $user->id === $booking->instructor_id)
            && $booking->isCancellable();

        if (! $canCancel) {
            return response()->json(['message' => 'Booking cannot be cancelled.'], 422);
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancellation_reason' => $request->input('cancellation_reason'),
            'cancelled_at' => now(),
        ]);

        // Notify the other party about cancellation
        $reason = $request->input('cancellation_reason', '');
        try {
            $otherUserId = ($user->id === $booking->learner_id) ? $booking->instructor_id : $booking->learner_id;
            $otherUser = User::find($otherUserId);
            if ($otherUser) {
                $otherUser->notify(new BookingCancelled($booking, $reason));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Cancel notification failed: ' . $e->getMessage());
        }

        return response()->json(['data' => $this->formatBooking($booking->fresh())]);
    }

    /**
     * Show single booking.
     */
    public function show(Booking $booking): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $booking->learner_id && $user->id !== $booking->instructor_id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $booking->load(['learner', 'instructor', 'suburb.state', 'review']);

        return response()->json(['data' => $this->formatBooking($booking)]);
    }

    private function formatBooking(Booking $b): array
    {
        $b->loadMissing(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state']);
        $location = null;
        if ($b->suburb) {
            $parts = array_filter([$b->suburb->name, $b->suburb->postcode, $b->suburb->state?->code ?? $b->suburb->state?->name ?? null]);
            $location = implode(' ', $parts);
        }
        return [
            'id' => $b->id,
            'learner' => $b->learner ? ['id' => $b->learner->id, 'name' => $b->learner->name, 'email' => $b->learner->email, 'phone' => $b->learner->phone] : null,
            'instructor' => $b->instructor ? ['id' => $b->instructor->id, 'name' => $b->instructor->name] : null,
            'suburb' => $b->suburb ? ['id' => $b->suburb->id, 'name' => $b->suburb->name, 'postcode' => $b->suburb->postcode, 'state_code' => $b->suburb->state?->code, 'location' => $location] : null,
            'type' => $b->type,
            'transmission' => $b->transmission,
            'scheduled_at' => $b->scheduled_at->toIso8601String(),
            'duration_minutes' => $b->duration_minutes,
            'amount' => (float) $b->amount,
            'test_pre_booked' => $b->test_pre_booked,
            'status' => $b->status,
            'learner_notes' => $b->learner_notes,
            'cancellation_reason' => $b->cancellation_reason,
            'cancelled_at' => $b->cancelled_at?->toIso8601String(),
            'review' => $b->relationLoaded('review') && $b->review
                ? ['id' => $b->review->id, 'rating' => $b->review->rating, 'comment' => $b->review->comment]
                : null,
            'payment_status' => $b->status === Booking::STATUS_COMPLETED ? 'PROCESSED' : ($b->status === Booking::STATUS_CANCELLED ? 'RETURNED' : null),
        ];
    }
}

<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Notifications\AdminBookingAlert;
use App\Notifications\BookingProposed;
use App\Services\BookingAvailabilityService;
use App\Traits\NotifiesAdmin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookingProposalController extends Controller
{
    use NotifiesAdmin;

    public function __construct(
        protected BookingAvailabilityService $availabilityService
    ) {}

    /**
     * Create one or more proposed bookings (instructor proposes to learner).
     * Proposals hold the slot until proposal_expires_at (24h or booking start, whichever first).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'proposals' => ['required', 'array', 'min:1'],
            'proposals.*.learner_id' => ['required', 'exists:users,id'],
            'proposals.*.suburb_id' => ['nullable', 'exists:suburbs,id'],
            'proposals.*.type' => ['required', Rule::in([Booking::TYPE_LESSON, Booking::TYPE_TEST_PACKAGE])],
            'proposals.*.transmission' => ['required', Rule::in(['auto', 'manual'])],
            'proposals.*.scheduled_at' => ['required', 'date', 'after:now'],
            'proposals.*.duration_minutes' => ['nullable', 'integer', 'min:30', 'max:180'],
        ]);

        $instructorId = Auth::id();
        $profile = Auth::user()->instructorProfile;
        if (! $profile || ! $profile->is_active) {
            return response()->json(['message' => 'Instructor profile not active.'], 422);
        }

        $created = [];
        foreach ($request->input('proposals') as $p) {
            $scheduledAt = Carbon::parse($p['scheduled_at']);
            $duration = $p['duration_minutes'] ?? $profile->lesson_duration_minutes ?? 60;
            $slots = $this->availabilityService->getAvailableSlots($profile, $scheduledAt->format('Y-m-d'));
            $timeKey = $scheduledAt->format('H:i');
            $allowed = collect($slots)->pluck('time')->contains($timeKey);
            if (! $allowed) {
                continue;
            }
            $amount = ($p['type'] ?? Booking::TYPE_LESSON) === Booking::TYPE_TEST_PACKAGE
                ? $profile->test_package_price
                : $profile->lesson_price;
            $expiresAt = Carbon::now()->addHours(24);
            if ($scheduledAt->lt($expiresAt)) {
                $expiresAt = $scheduledAt;
            }
            $booking = Booking::create([
                'learner_id' => $p['learner_id'],
                'instructor_id' => $instructorId,
                'suburb_id' => $p['suburb_id'] ?? null,
                'type' => $p['type'],
                'transmission' => $p['transmission'],
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'amount' => $amount ?? 0,
                'test_pre_booked' => false,
                'status' => Booking::STATUS_PROPOSED,
                'proposal_expires_at' => $expiresAt,
            ]);
            $booking->load(['learner:id,name,email,phone', 'suburb']);
            $created[] = $booking;

            // Notify the learner about the proposal
            try {
                $learner = User::find($p['learner_id']);
                if ($learner) {
                    $learner->notify(new BookingProposed($booking, Auth::user()->name));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Proposal notification failed: ' . $e->getMessage());
            }

            // Notify admin about the proposal
            $this->notifyAdminAboutBooking($booking, AdminBookingAlert::EVENT_PROPOSED);
        }

        return response()->json([
            'message' => count($created) . ' proposal(s) sent.',
            'data' => $created,
        ], 201);
    }
}

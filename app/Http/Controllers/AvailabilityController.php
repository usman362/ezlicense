<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use App\Services\BookingAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(
        protected BookingAvailabilityService $availabilityService
    ) {}

    /**
     * Get available dates for an instructor (for calendar / date picker).
     */
    public function dates(Request $request, InstructorProfile $instructorProfile): JsonResponse
    {
        $days = min(90, max(7, (int) $request->input('days', 30)));
        $dates = $this->availabilityService->getAvailableDates($instructorProfile, $days);

        return response()->json(['data' => $dates]);
    }

    /**
     * Get available time slots for an instructor on a specific date.
     */
    public function slots(Request $request, InstructorProfile $instructorProfile): JsonResponse
    {
        $date = $request->input('date');
        if (! $date) {
            return response()->json(['message' => 'Date required'], 422);
        }

        $slots = $this->availabilityService->getAvailableSlots($instructorProfile, $date);

        return response()->json(['data' => $slots]);
    }
}

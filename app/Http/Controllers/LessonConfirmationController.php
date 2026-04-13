<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LessonConfirmationController extends Controller
{
    /**
     * Show the lesson confirmation page.
     * Accessed via a unique token — no login required.
     */
    public function show(string $token)
    {
        $booking = Booking::where('confirmation_token', $token)
            ->with(['instructor:id,name', 'learner:id,name,first_name', 'suburb.state'])
            ->first();

        if (!$booking) {
            return view('lesson-confirmation', [
                'status' => 'invalid',
                'booking' => null,
            ]);
        }

        // Already confirmed
        if ($booking->isLearnerConfirmed()) {
            return view('lesson-confirmation', [
                'status' => 'already_confirmed',
                'booking' => $booking,
            ]);
        }

        return view('lesson-confirmation', [
            'status' => 'pending',
            'booking' => $booking,
            'token' => $token,
        ]);
    }

    /**
     * Process the confirmation.
     * Records timestamp, IP address and user agent as forensic evidence.
     */
    public function confirm(Request $request, string $token)
    {
        $booking = Booking::where('confirmation_token', $token)
            ->with(['instructor:id,name', 'learner:id,name'])
            ->first();

        if (!$booking) {
            return view('lesson-confirmation', [
                'status' => 'invalid',
                'booking' => null,
            ]);
        }

        if ($booking->isLearnerConfirmed()) {
            return view('lesson-confirmation', [
                'status' => 'already_confirmed',
                'booking' => $booking,
            ]);
        }

        // Record confirmation with forensic evidence
        $booking->recordConfirmation(
            ip: $request->ip(),
            userAgent: $request->userAgent()
        );

        Log::info('Lesson confirmation received', [
            'booking_id' => $booking->id,
            'learner_id' => $booking->learner_id,
            'instructor_id' => $booking->instructor_id,
            'ip' => $request->ip(),
            'confirmed_at' => $booking->fresh()->learner_confirmed_at->toIso8601String(),
        ]);

        return view('lesson-confirmation', [
            'status' => 'confirmed',
            'booking' => $booking->fresh(),
        ]);
    }
}

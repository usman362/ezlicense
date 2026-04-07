<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\IcsGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarFeedController extends Controller
{
    /**
     * Serve ICS feed for an instructor (token-authenticated for calendar subscriptions).
     */
    public function instructorFeed(string $token): Response
    {
        $user = User::where('calendar_token', $token)
            ->where('role', 'instructor')
            ->first();

        if (!$user) {
            abort(404, 'Calendar feed not found.');
        }

        $ics = IcsGenerator::feedForInstructor($user->id);

        return response($ics, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="securelicences-bookings.ics"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Serve ICS feed for a learner.
     */
    public function learnerFeed(string $token): Response
    {
        $user = User::where('calendar_token', $token)->first();

        if (!$user) {
            abort(404, 'Calendar feed not found.');
        }

        $ics = IcsGenerator::feedForLearner($user->id);

        return response($ics, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="my-driving-lessons.ics"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Generate/regenerate the user's calendar token and return subscribe URLs.
     */
    public function generateToken(Request $request)
    {
        $user = $request->user();

        if (!$user->calendar_token) {
            $user->calendar_token = \Illuminate\Support\Str::random(48);
            $user->save();
        }

        $role = $user->role ?? 'learner';
        $feedPath = $role === 'instructor'
            ? '/calendar/instructor/' . $user->calendar_token . '/feed.ics'
            : '/calendar/learner/' . $user->calendar_token . '/feed.ics';

        $webcalUrl = 'webcal://' . request()->getHost() . $feedPath;
        $httpsUrl = url($feedPath);
        $googleUrl = 'https://calendar.google.com/calendar/r?cid=' . urlencode($httpsUrl);

        return response()->json([
            'calendar_token' => $user->calendar_token,
            'webcal_url' => $webcalUrl,
            'https_url' => $httpsUrl,
            'google_url' => $googleUrl,
            'instructions' => [
                'apple' => 'Open the webcal:// link on your iPhone/Mac to subscribe in Apple Calendar.',
                'google' => 'Click the Google Calendar link or add by URL in Google Calendar settings.',
                'outlook' => 'Use "Subscribe from web" in Outlook and paste the HTTPS URL.',
            ],
        ]);
    }

    /**
     * Regenerate the calendar token (invalidates old subscriptions).
     */
    public function regenerateToken(Request $request)
    {
        $user = $request->user();
        $user->calendar_token = \Illuminate\Support\Str::random(48);
        $user->save();

        return $this->generateToken($request);
    }

    /**
     * Download single booking as .ics file.
     */
    public function downloadBooking(Request $request, \App\Models\Booking $booking)
    {
        $user = $request->user();

        if ($booking->learner_id !== $user->id && $booking->instructor_id !== $user->id) {
            abort(403);
        }

        $ics = IcsGenerator::forBooking($booking);

        return response($ics, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="lesson-' . $booking->id . '.ics"');
    }
}

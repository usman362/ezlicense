<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    public function __construct(
        protected GoogleCalendarService $calendarService
    ) {}

    /**
     * Redirect the user to Google's OAuth consent screen to connect their calendar.
     */
    public function connect(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!GoogleCalendarService::isPackageInstalled()) {
            return redirect()->back()->with('error', 'Google Calendar integration is not yet available.');
        }

        try {
            $authUrl = $this->calendarService->getAuthUrl($user);
            return redirect()->away($authUrl);
        } catch (\Throwable $e) {
            Log::error('Google Calendar connect error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to connect to Google Calendar. Please try again.');
        }
    }

    /**
     * Handle the OAuth callback from Google after the user grants permission.
     */
    public function callback(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->has('error')) {
            Log::warning('Google Calendar OAuth denied for user ' . $user->id . ': ' . $request->input('error'));
            return redirect()->route(
                $user->isInstructor() ? 'instructor.settings.calendar-settings' : 'learner.calendar'
            )->with('error', 'Google Calendar connection was cancelled.');
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route(
                $user->isInstructor() ? 'instructor.settings.calendar-settings' : 'learner.calendar'
            )->with('error', 'Invalid callback from Google. Please try again.');
        }

        try {
            $this->calendarService->handleCallback($user, $code);

            return redirect()->route(
                $user->isInstructor() ? 'instructor.settings.calendar-settings' : 'learner.calendar'
            )->with('success', 'Google Calendar connected successfully! Your bookings will now sync automatically.');
        } catch (\Throwable $e) {
            Log::error('Google Calendar callback error for user ' . $user->id . ': ' . $e->getMessage());
            return redirect()->route(
                $user->isInstructor() ? 'instructor.settings.calendar-settings' : 'learner.calendar'
            )->with('error', 'Failed to connect Google Calendar. Please try again.');
        }
    }

    /**
     * Disconnect Google Calendar and remove stored tokens.
     */
    public function disconnect(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            $this->calendarService->disconnect($user);

            return response()->json([
                'message' => 'Google Calendar disconnected successfully.',
                'connected' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Calendar disconnect error for user ' . $user->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Failed to disconnect. Please try again.'], 500);
        }
    }

    /**
     * Return the current Google Calendar sync status for the authenticated user.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        $connected = $this->calendarService->isConnected($user);

        return response()->json([
            'connected' => $connected,
            'sync_enabled' => (bool) $user->google_calendar_sync_enabled,
            'calendar_id' => $user->google_calendar_id,
            'last_synced_at' => $user->google_calendar_last_synced_at?->toIso8601String(),
            'package_installed' => GoogleCalendarService::isPackageInstalled(),
        ]);
    }

    /**
     * Manually trigger a full sync of all upcoming bookings to Google Calendar.
     */
    public function syncNow(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->calendarService->isConnected($user)) {
            return response()->json([
                'message' => 'Google Calendar is not connected. Please connect first.',
            ], 422);
        }

        try {
            $count = $this->calendarService->syncAllUpcoming($user);

            return response()->json([
                'message' => "Synced {$count} upcoming booking(s) to Google Calendar.",
                'synced_count' => $count,
                'last_synced_at' => $user->fresh()->google_calendar_last_synced_at?->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Calendar sync error for user ' . $user->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Sync failed. Please try again.'], 500);
        }
    }
}

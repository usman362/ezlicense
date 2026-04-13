<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    /**
     * Check if the Google API client library is installed.
     */
    public static function isPackageInstalled(): bool
    {
        return class_exists(\Google_Client::class);
    }

    /**
     * Get the Google OAuth authorization URL for the user to connect their calendar.
     */
    public function getAuthUrl(User $user): string
    {
        $client = $this->createBaseClient();
        $client->setState($user->id);

        return $client->createAuthUrl();
    }

    /**
     * Handle the OAuth callback by exchanging the auth code for tokens and storing them.
     */
    public function handleCallback(User $user, string $authCode): void
    {
        $client = $this->createBaseClient();
        $token = $client->fetchAccessTokenWithAuthCode($authCode);

        if (isset($token['error'])) {
            throw new \RuntimeException('Google OAuth error: ' . ($token['error_description'] ?? $token['error']));
        }

        $user->update([
            'google_calendar_token' => $token,
            'google_calendar_sync_enabled' => true,
            'google_calendar_id' => 'primary',
        ]);
    }

    /**
     * Push a booking to the user's Google Calendar as an event.
     * Returns the Google event ID or null on failure.
     */
    public function pushBookingToCalendar(User $user, Booking $booking): ?string
    {
        if (!$this->isConnected($user)) {
            return null;
        }

        try {
            $service = $this->getCalendarService($user);
            $event = $this->buildEventFromBooking($booking, $user);

            $calendarId = $user->google_calendar_id ?: 'primary';
            $createdEvent = $service->events->insert($calendarId, $event);

            return $createdEvent->getId();
        } catch (\Throwable $e) {
            Log::warning('Google Calendar push failed for user ' . $user->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing event on Google Calendar for a booking.
     */
    public function updateCalendarEvent(User $user, Booking $booking, ?string $googleEventId = null): void
    {
        if (!$this->isConnected($user) || !$googleEventId) {
            return;
        }

        try {
            $service = $this->getCalendarService($user);
            $event = $this->buildEventFromBooking($booking, $user);

            $calendarId = $user->google_calendar_id ?: 'primary';
            $service->events->update($calendarId, $googleEventId, $event);
        } catch (\Throwable $e) {
            Log::warning('Google Calendar update failed for user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete an event from Google Calendar (e.g. on booking cancellation).
     */
    public function deleteCalendarEvent(User $user, string $googleEventId): void
    {
        if (!$this->isConnected($user)) {
            return;
        }

        try {
            $service = $this->getCalendarService($user);
            $calendarId = $user->google_calendar_id ?: 'primary';
            $service->events->delete($calendarId, $googleEventId);
        } catch (\Throwable $e) {
            Log::warning('Google Calendar delete failed for user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Pull events from Google Calendar within a time window to check for conflicts.
     * Returns an array of ['start' => Carbon, 'end' => Carbon, 'summary' => string].
     */
    public function getConflicts(User $user, Carbon $start, Carbon $end): array
    {
        if (!$this->isConnected($user)) {
            return [];
        }

        try {
            $service = $this->getCalendarService($user);
            $calendarId = $user->google_calendar_id ?: 'primary';

            $events = $service->events->listEvents($calendarId, [
                'timeMin' => $start->toRfc3339String(),
                'timeMax' => $end->toRfc3339String(),
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'maxResults' => 250,
            ]);

            $conflicts = [];
            foreach ($events->getItems() as $event) {
                $eventStart = $event->getStart();
                $eventEnd = $event->getEnd();

                // Skip all-day events (they have 'date' instead of 'dateTime')
                if (!$eventStart->getDateTime()) {
                    continue;
                }

                $conflicts[] = [
                    'start' => Carbon::parse($eventStart->getDateTime()),
                    'end' => Carbon::parse($eventEnd->getDateTime()),
                    'summary' => $event->getSummary() ?? 'Busy',
                ];
            }

            return $conflicts;
        } catch (\Throwable $e) {
            Log::warning('Google Calendar conflict check failed for user ' . $user->id . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Disconnect Google Calendar for a user by clearing tokens and disabling sync.
     */
    public function disconnect(User $user): void
    {
        // Try to revoke the token before clearing
        if ($user->google_calendar_token) {
            try {
                $client = $this->getClient($user);
                $client->revokeToken();
            } catch (\Throwable $e) {
                // Revocation failure is non-critical
                Log::info('Google token revocation skipped for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $user->update([
            'google_calendar_token' => null,
            'google_calendar_id' => null,
            'google_calendar_sync_enabled' => false,
            'google_calendar_last_synced_at' => null,
        ]);
    }

    /**
     * Check if the user has a valid (non-null) Google Calendar token.
     */
    public function isConnected(User $user): bool
    {
        return !empty($user->google_calendar_token)
            && $user->google_calendar_sync_enabled;
    }

    /**
     * Push all upcoming bookings for a user to Google Calendar.
     * Returns the count of events successfully pushed.
     */
    public function syncAllUpcoming(User $user): int
    {
        if (!$this->isConnected($user)) {
            return 0;
        }

        $query = $user->isInstructor()
            ? Booking::where('instructor_id', $user->id)
            : Booking::where('learner_id', $user->id);

        $bookings = $query
            ->where('scheduled_at', '>=', now())
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $existingEventId = $user->isInstructor()
                ? $booking->google_event_id
                : $booking->google_event_id_learner;

            if ($existingEventId) {
                // Update existing event
                $this->updateCalendarEvent($user, $booking, $existingEventId);
                $count++;
            } else {
                // Create new event
                $eventId = $this->pushBookingToCalendar($user, $booking);
                if ($eventId) {
                    $field = $user->isInstructor() ? 'google_event_id' : 'google_event_id_learner';
                    $booking->update([$field => $eventId]);
                    $count++;
                }
            }
        }

        $user->update(['google_calendar_last_synced_at' => now()]);

        return $count;
    }

    /**
     * Build a Google Calendar Event object from a Booking.
     */
    private function buildEventFromBooking(Booking $booking, User $user): \Google_Service_Calendar_Event
    {
        $booking->loadMissing(['learner', 'instructor', 'suburb.state']);

        $isInstructor = $user->id === $booking->instructor_id;

        // Event title
        $typeLabel = $booking->type === Booking::TYPE_TEST_PACKAGE ? 'Driving Test Package' : 'Driving Lesson';
        $otherParty = $isInstructor
            ? ($booking->learner->name ?? 'Learner')
            : ($booking->instructor->name ?? 'Instructor');
        $summary = "{$typeLabel} - {$otherParty}";

        // Event description
        $descriptionLines = [
            "SecureLicences Booking #{$booking->id}",
            "Type: {$typeLabel}",
            "Transmission: " . ucfirst($booking->transmission ?? 'Auto'),
            "Duration: {$booking->duration_minutes} minutes",
        ];

        if ($isInstructor) {
            $descriptionLines[] = "Learner: " . ($booking->learner->name ?? 'N/A');
            $descriptionLines[] = "Phone: " . ($booking->learner->phone ?? 'N/A');
        } else {
            $descriptionLines[] = "Instructor: " . ($booking->instructor->name ?? 'N/A');
        }

        if ($booking->suburb) {
            $location = trim(implode(' ', array_filter([
                $booking->suburb->name,
                $booking->suburb->postcode,
                $booking->suburb->state?->code,
            ])));
            $descriptionLines[] = "Location: {$location}";
        }

        if ($booking->learner_notes) {
            $descriptionLines[] = "Notes: {$booking->learner_notes}";
        }

        $descriptionLines[] = '';
        $descriptionLines[] = 'Managed by SecureLicences';

        $description = implode("\n", $descriptionLines);

        // Location string
        $locationStr = null;
        if ($booking->suburb) {
            $locationStr = trim(implode(', ', array_filter([
                $booking->suburb->name,
                $booking->suburb->state?->name ?? $booking->suburb->state?->code ?? null,
                $booking->suburb->postcode,
                'Australia',
            ])));
        }

        // Build the event
        $startTime = $booking->scheduled_at->copy()->setTimezone('Australia/Sydney');
        $endTime = $startTime->copy()->addMinutes($booking->duration_minutes ?: 60);

        $event = new \Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $startTime->toRfc3339String(),
                'timeZone' => 'Australia/Sydney',
            ],
            'end' => [
                'dateTime' => $endTime->toRfc3339String(),
                'timeZone' => 'Australia/Sydney',
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 60],
                    ['method' => 'popup', 'minutes' => 15],
                ],
            ],
        ]);

        if ($locationStr) {
            $event->setLocation($locationStr);
        }

        // Color: blue for lessons, red for test packages
        if ($booking->type === Booking::TYPE_TEST_PACKAGE) {
            $event->setColorId('11'); // red / tomato
        }

        return $event;
    }

    /**
     * Create a base Google_Client configured with app credentials (no user tokens).
     */
    private function createBaseClient(): \Google_Client
    {
        if (!self::isPackageInstalled()) {
            throw new \RuntimeException(
                'Google API client not installed. Run: composer require google/apiclient'
            );
        }

        $client = new \Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(url(config('services.google.redirect_uri', '/google-calendar/callback')));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);

        $scopes = config('services.google.scopes', ['https://www.googleapis.com/auth/calendar']);
        $client->addScope($scopes);

        return $client;
    }

    /**
     * Get a Google_Client authenticated for a specific user, handling token refresh.
     */
    private function getClient(User $user): \Google_Client
    {
        $client = $this->createBaseClient();

        $token = $user->google_calendar_token;
        if (empty($token)) {
            throw new \RuntimeException('User has no Google Calendar token.');
        }

        $client->setAccessToken($token);

        // Refresh expired tokens automatically
        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();
            if (!$refreshToken && isset($token['refresh_token'])) {
                $refreshToken = $token['refresh_token'];
            }

            if ($refreshToken) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

                if (isset($newToken['error'])) {
                    // Token refresh failed - disconnect the user
                    Log::warning('Google token refresh failed for user ' . $user->id . ': ' . ($newToken['error_description'] ?? $newToken['error']));
                    $this->disconnect($user);
                    throw new \RuntimeException('Google Calendar token refresh failed. User has been disconnected.');
                }

                // Preserve the refresh token if the new response doesn't include one
                if (empty($newToken['refresh_token']) && $refreshToken) {
                    $newToken['refresh_token'] = $refreshToken;
                }

                $user->update(['google_calendar_token' => $newToken]);
                $client->setAccessToken($newToken);
            } else {
                // No refresh token available - disconnect
                $this->disconnect($user);
                throw new \RuntimeException('Google Calendar token expired and no refresh token available.');
            }
        }

        return $client;
    }

    /**
     * Get an authenticated Google Calendar service instance for a user.
     */
    private function getCalendarService(User $user): \Google_Service_Calendar
    {
        $client = $this->getClient($user);

        return new \Google_Service_Calendar($client);
    }
}

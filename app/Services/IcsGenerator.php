<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class IcsGenerator
{
    /**
     * Generate ICS content for a single booking.
     */
    public static function forBooking(Booking $booking): string
    {
        $booking->loadMissing(['learner', 'instructor', 'suburb']);

        $start = Carbon::parse($booking->scheduled_at);
        $end = $start->copy()->addMinutes($booking->duration_minutes ?? 60);
        $uid = 'booking-' . $booking->id . '@securelicences.com.au';

        $learnerName = $booking->learner->name ?? 'Learner';
        $instructorName = $booking->instructor->name ?? 'Instructor';
        $suburbName = $booking->suburb->name ?? '';
        $postcode = $booking->suburb->postcode ?? '';

        $summary = 'Driving Lesson - ' . $learnerName;
        $description = "Driving lesson with {$learnerName}\\n"
            . "Type: " . ucfirst(str_replace('_', ' ', $booking->type ?? 'lesson')) . "\\n"
            . "Duration: " . ($booking->duration_minutes ?? 60) . " minutes\\n"
            . "Transmission: " . ucfirst($booking->transmission ?? 'auto') . "\\n"
            . "Instructor: {$instructorName}";
        $location = $suburbName . ($postcode ? ", {$postcode}" : '') . ', Australia';

        return self::buildIcs($uid, $start, $end, $summary, $description, $location, now());
    }

    /**
     * Generate ICS feed for an instructor's upcoming bookings.
     */
    public static function feedForInstructor(int $instructorUserId): string
    {
        $bookings = Booking::where('instructor_id', $instructorUserId)
            ->whereIn('status', ['confirmed', 'proposed'])
            ->where('scheduled_at', '>=', now()->subDays(7))
            ->where('scheduled_at', '<=', now()->addDays(90))
            ->with(['learner', 'suburb'])
            ->orderBy('scheduled_at')
            ->get();

        $events = '';
        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->scheduled_at);
            $end = $start->copy()->addMinutes($booking->duration_minutes ?? 60);
            $uid = 'booking-' . $booking->id . '@securelicences.com.au';

            $learnerName = $booking->learner->name ?? 'Learner';
            $suburbName = $booking->suburb->name ?? '';
            $postcode = $booking->suburb->postcode ?? '';

            $summary = 'Driving Lesson - ' . $learnerName;
            $description = ucfirst(str_replace('_', ' ', $booking->type ?? 'lesson'))
                . " | " . ($booking->duration_minutes ?? 60) . "min"
                . " | " . ucfirst($booking->transmission ?? 'auto');
            $location = $suburbName . ($postcode ? " {$postcode}" : '');

            $events .= self::buildEvent($uid, $start, $end, $summary, $description, $location, $booking->updated_at ?? now());
        }

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//Secure Licences//Booking Calendar//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "X-WR-CALNAME:Secure Licences Bookings\r\n"
            . "X-WR-TIMEZONE:Australia/Sydney\r\n"
            . $events
            . "END:VCALENDAR\r\n";
    }

    /**
     * Generate ICS feed for a learner's upcoming bookings.
     */
    public static function feedForLearner(int $learnerUserId): string
    {
        $bookings = Booking::where('learner_id', $learnerUserId)
            ->whereIn('status', ['confirmed', 'proposed'])
            ->where('scheduled_at', '>=', now()->subDays(7))
            ->where('scheduled_at', '<=', now()->addDays(90))
            ->with(['instructor', 'suburb'])
            ->orderBy('scheduled_at')
            ->get();

        $events = '';
        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->scheduled_at);
            $end = $start->copy()->addMinutes($booking->duration_minutes ?? 60);
            $uid = 'booking-' . $booking->id . '@securelicences.com.au';

            $instructorName = $booking->instructor->name ?? 'Instructor';
            $suburbName = $booking->suburb->name ?? '';
            $postcode = $booking->suburb->postcode ?? '';

            $summary = 'Driving Lesson with ' . $instructorName;
            $description = ucfirst(str_replace('_', ' ', $booking->type ?? 'lesson'))
                . " | " . ($booking->duration_minutes ?? 60) . "min";
            $location = $suburbName . ($postcode ? " {$postcode}" : '');

            $events .= self::buildEvent($uid, $start, $end, $summary, $description, $location, $booking->updated_at ?? now());
        }

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//Secure Licences//Booking Calendar//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "X-WR-CALNAME:My Driving Lessons\r\n"
            . "X-WR-TIMEZONE:Australia/Sydney\r\n"
            . $events
            . "END:VCALENDAR\r\n";
    }

    private static function buildIcs(string $uid, Carbon $start, Carbon $end, string $summary, string $description, string $location, Carbon $stamp): string
    {
        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//Secure Licences//Booking Calendar//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:REQUEST\r\n"
            . self::buildEvent($uid, $start, $end, $summary, $description, $location, $stamp)
            . "END:VCALENDAR\r\n";
    }

    private static function buildEvent(string $uid, Carbon $start, Carbon $end, string $summary, string $description, string $location, Carbon $stamp): string
    {
        return "BEGIN:VEVENT\r\n"
            . "UID:" . $uid . "\r\n"
            . "DTSTAMP:" . $stamp->utc()->format('Ymd\THis\Z') . "\r\n"
            . "DTSTART:" . $start->utc()->format('Ymd\THis\Z') . "\r\n"
            . "DTEND:" . $end->utc()->format('Ymd\THis\Z') . "\r\n"
            . "SUMMARY:" . self::escapeIcs($summary) . "\r\n"
            . "DESCRIPTION:" . self::escapeIcs($description) . "\r\n"
            . "LOCATION:" . self::escapeIcs($location) . "\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "BEGIN:VALARM\r\n"
            . "TRIGGER:-PT30M\r\n"
            . "ACTION:DISPLAY\r\n"
            . "DESCRIPTION:Driving lesson in 30 minutes\r\n"
            . "END:VALARM\r\n"
            . "END:VEVENT\r\n";
    }

    private static function escapeIcs(string $text): string
    {
        return str_replace(["\r\n", "\n", "\r", ",", ";"], ["\\n", "\\n", "\\n", "\\,", "\\;"], $text);
    }
}

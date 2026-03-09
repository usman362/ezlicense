<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\InstructorProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingAvailabilityService
{
    /**
     * Get available time slots for an instructor on a given date.
     * Considers weekly slots and blocks (blocked dates / extra availability).
     */
    public function getAvailableSlots(InstructorProfile $instructor, string $date): array
    {
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek; // 0=Sun, 6=Sat

        $slots = $instructor->availabilitySlots()
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $blocks = $instructor->availabilityBlocks()
            ->where('date', $date)
            ->get();

        $wholeDayBlocked = $blocks->where('is_available', false)->contains(fn ($b) => $b->start_time === null);
        if ($wholeDayBlocked) {
            return [];
        }

        $booked = Booking::where('instructor_id', $instructor->user_id)
            ->whereDate('scheduled_at', $date)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->get()
            ->keyBy(fn ($b) => $b->scheduled_at->format('H:i'));

        $duration = $instructor->lesson_duration_minutes ?: 60;
        $result = [];

        foreach ($slots as $slot) {
            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $slot->start_time);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $slot->end_time);
            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $timeKey = $start->format('H:i');
                if (! $booked->has($timeKey)) {
                    $result[] = [
                        'time' => $timeKey,
                        'datetime' => $start->format('Y-m-d H:i:s'),
                    ];
                }
                $start->addMinutes($duration);
            }
        }

        foreach ($blocks->where('is_available', true) as $block) {
            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $block->start_time);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $block->end_time);
            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $timeKey = $start->format('H:i');
                if (! $booked->has($timeKey) && ! in_array($timeKey, array_column($result, 'time'), true)) {
                    $result[] = [
                        'time' => $timeKey,
                        'datetime' => $start->format('Y-m-d H:i:s'),
                    ];
                }
                $start->addMinutes($duration);
            }
        }

        usort($result, fn ($a, $b) => strcmp($a['time'], $b['time']));

        return $result;
    }

    /**
     * Get available dates for the next N days for an instructor.
     */
    public function getAvailableDates(InstructorProfile $instructor, int $days = 30): Collection
    {
        $dates = collect();
        $start = Carbon::today();
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $slots = $this->getAvailableSlots($instructor, $date->format('Y-m-d'));
            if (count($slots) > 0) {
                $dates->push([
                    'date' => $date->format('Y-m-d'),
                    'slots_count' => count($slots),
                ]);
            }
        }

        return $dates;
    }
}

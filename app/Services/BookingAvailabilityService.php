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
     *
     * Honours these calendar settings on the instructor profile:
     *   - lesson_durations          (multi) — uses smallest as the slot step, but
     *                                         tags each slot with the set of durations
     *                                         that fit before the slot end.
     *   - min_prior_notice_hours    — drops slots starting sooner than that.
     *   - travel_buffer_same_mins   — adds breathing room around existing bookings.
     *   - smart_scheduling_enabled  — when on AND there's at least one booking that
     *                                 day, prioritises slots within
     *                                 smart_scheduling_buffer_hrs of an existing
     *                                 booking (returned first; others omitted to
     *                                 cluster bookings together).
     *
     * @param  string  $date            Y-m-d format
     * @param  int|null $durationOverride  When set, slot length used is exactly this
     *                                     value (in minutes). Defaults to smallest in
     *                                     lesson_durations or lesson_duration_minutes.
     */
    public function getAvailableSlots(InstructorProfile $instructor, string $date, ?int $durationOverride = null): array
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

        // ── Settings ─────────────────────────────────────────────────────────
        $allowedDurations = $this->resolveAllowedDurations($instructor);
        $stepDuration = $durationOverride
            ? max(15, (int) $durationOverride)
            : min($allowedDurations);
        $minNoticeHours = (int) ($instructor->min_prior_notice_hours ?? 5);
        $travelBuffer = (int) ($instructor->travel_buffer_same_mins ?? 30);
        $smartEnabled = (bool) ($instructor->smart_scheduling_enabled ?? true);
        $smartBufferHrs = max(1, (int) ($instructor->smart_scheduling_buffer_hrs ?? 1));

        $earliestAllowed = now()->copy()->addHours($minNoticeHours);

        // ── Existing bookings on this day (used for travel buffer & clustering) ──
        // An unpaid card checkout holds its slot as PROPOSED. If the learner abandons
        // Stripe, that hold would otherwise block the slot forever — so we stop counting
        // a PROPOSED + still-unpaid booking as busy once it's older than the hold window.
        $holdMinutes = (int) \App\Models\SiteSetting::get('proposed_hold_minutes', 60);
        $staleBefore = now()->copy()->subMinutes(max(1, $holdMinutes));

        $bookings = Booking::where('instructor_id', $instructor->user_id)
            ->whereDate('scheduled_at', $date)
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_PROPOSED,
                Booking::STATUS_INSTRUCTOR_ARRIVED,
                Booking::STATUS_IN_PROGRESS,
            ])
            ->where(function ($q) use ($staleBefore) {
                // Keep the slot busy unless it's an abandoned (unpaid, proposed, old) hold.
                $q->where('status', '!=', Booking::STATUS_PROPOSED)
                  ->orWhere('payment_status', '!=', Booking::PAYMENT_PENDING)
                  ->orWhere('created_at', '>=', $staleBefore);
            })
            ->get(['scheduled_at', 'duration_minutes']);

        // Each booking becomes a busy window inflated by travel buffer on both sides.
        $busyWindows = $bookings->map(function ($b) use ($travelBuffer) {
            $start = $b->scheduled_at->copy()->subMinutes($travelBuffer);
            $end = $b->scheduled_at->copy()->addMinutes(((int) ($b->duration_minutes ?? 60)) + $travelBuffer);
            return ['start' => $start, 'end' => $end];
        });

        // ── Generate raw slots from weekly availability + extra availability blocks ──
        $raw = collect();
        foreach ($slots as $slot) {
            $raw = $raw->merge($this->expandWindow($date, $slot->start_time, $slot->end_time, $stepDuration));
        }
        foreach ($blocks->where('is_available', true) as $block) {
            $raw = $raw->merge($this->expandWindow($date, $block->start_time, $block->end_time, $stepDuration));
        }

        // ── Filter: min prior notice + collision with busy windows ──
        $filtered = $raw
            ->unique('time')
            ->filter(function ($slot) use ($earliestAllowed, $busyWindows, $stepDuration) {
                $start = Carbon::parse($slot['datetime']);
                $end = $start->copy()->addMinutes($stepDuration);

                if ($start->lt($earliestAllowed)) {
                    return false;
                }
                foreach ($busyWindows as $w) {
                    if ($start->lt($w['end']) && $end->gt($w['start'])) {
                        return false;
                    }
                }
                return true;
            })
            ->values();

        // ── Smart Scheduling: cluster around existing bookings ──
        if ($smartEnabled && $bookings->isNotEmpty()) {
            $clusterWindow = $smartBufferHrs * 60;
            $bookingTimes = $bookings->map(fn ($b) => $b->scheduled_at->copy());

            $clustered = $filtered->filter(function ($slot) use ($bookingTimes, $clusterWindow) {
                $start = Carbon::parse($slot['datetime']);
                foreach ($bookingTimes as $bt) {
                    if (abs($bt->diffInMinutes($start, false)) <= $clusterWindow) {
                        return true;
                    }
                }
                return false;
            })->values();

            // Only collapse to clustered subset if it has slots — otherwise we'd
            // return zero slots on busy days, which is worse UX.
            if ($clustered->isNotEmpty()) {
                $filtered = $clustered;
            }
        }

        // ── Annotate each slot with which of the instructor's offered durations
        // actually fit at this start time (no collision with busy windows). ──
        $result = $filtered->map(function ($slot) use ($allowedDurations, $busyWindows) {
            $start = Carbon::parse($slot['datetime']);
            $fits = [];
            foreach ($allowedDurations as $d) {
                $end = $start->copy()->addMinutes($d);
                $collision = false;
                foreach ($busyWindows as $w) {
                    if ($start->lt($w['end']) && $end->gt($w['start'])) {
                        $collision = true;
                        break;
                    }
                }
                if (! $collision) {
                    $fits[] = $d;
                }
            }
            return [
                'time' => $slot['time'],
                'datetime' => $slot['datetime'],
                'durations_minutes' => $fits, // which durations fit here
            ];
        })->sortBy('time')->values()->all();

        return $result;
    }

    /**
     * Available dates within the instructor's configured booking window.
     *
     * - Starts: today + min_prior_notice_hours (rounded up to days)
     * - Ends:   today + max_advance_notice_days
     * - The passed-in $days arg becomes a CEILING, never extends past instructor's max.
     */
    public function getAvailableDates(InstructorProfile $instructor, int $days = 30): Collection
    {
        $minNoticeHours = (int) ($instructor->min_prior_notice_hours ?? 5);
        $maxAdvance = (int) ($instructor->max_advance_notice_days ?? 75);

        $startOffsetDays = (int) ceil($minNoticeHours / 24);
        $effectiveDays = max(1, min($days, max(1, $maxAdvance - $startOffsetDays + 1)));

        $dates = collect();
        $start = Carbon::today()->addDays($startOffsetDays);
        for ($i = 0; $i < $effectiveDays; $i++) {
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

    /**
     * Resolve an instructor's allowed lesson durations as a sorted int array.
     * Falls back gracefully when lesson_durations is empty/null.
     */
    public function resolveAllowedDurations(InstructorProfile $instructor): array
    {
        $set = $instructor->lesson_durations ?? null;
        if (is_array($set) && count($set) > 0) {
            $set = array_values(array_unique(array_map('intval', $set)));
            sort($set);
            return $set;
        }
        $single = (int) ($instructor->lesson_duration_minutes ?? 60);
        $set = array_values(array_unique([60, 120, $single > 0 ? $single : 60]));
        sort($set);
        return $set;
    }

    /**
     * Internal: expand a [start_time, end_time] window into discrete step-sized slots.
     */
    private function expandWindow(Carbon $date, ?string $startTime, ?string $endTime, int $step): array
    {
        if (! $startTime || ! $endTime) {
            return [];
        }
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);
        $out = [];
        while ($start->copy()->addMinutes($step)->lte($end)) {
            $out[] = [
                'time' => $start->format('H:i'),
                'datetime' => $start->format('Y-m-d H:i:s'),
            ];
            $start->addMinutes($step);
        }
        return $out;
    }
}

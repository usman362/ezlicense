<?php

namespace App\Services;

use App\Models\InstructorProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Calculate statement periods for an instructor based on their `payout_frequency`.
 *
 *   weekly           → Sun 00:00 → Sat 23:59  (Australia/Sydney)
 *   fortnightly      → 14-day windows, aligned to the SAME reference Sun as weekly
 *   every_four_weeks → 28-day windows, aligned likewise
 *
 * All periods are anchored to Sunday-Saturday week boundaries so they line up
 * cleanly with how the existing PayoutService runs weekly payouts.
 */
class StatementPeriodService
{
    public const TZ = 'Australia/Sydney';

    /**
     * Recent N periods for an instructor — most recent first.
     * The "current" period (still in progress) is included as the first entry.
     *
     * @return Collection<int, array{
     *     key: string,           // YYYY-MM-DD start, used as URL identifier
     *     start: Carbon,         // local-tz inclusive start
     *     end: Carbon,           // local-tz inclusive end
     *     label: string,         // human label e.g. "10 - 16 May 2026"
     *     frequency: string,
     *     is_current: bool,      // true if 'today' falls within this period
     * }>
     */
    public function recent(InstructorProfile $profile, int $count = 12): Collection
    {
        $frequency = $this->normaliseFrequency($profile->payout_frequency ?? 'weekly');
        $today = Carbon::now(self::TZ)->startOfDay();

        // Find the current period's start (most recent Sunday rolled back as needed)
        $currentStart = $this->periodStartFor($today, $frequency);
        $stepDays = $this->stepDays($frequency);

        $periods = collect();
        for ($i = 0; $i < $count; $i++) {
            $start = $currentStart->copy()->subDays($stepDays * $i);
            $end = $start->copy()->addDays($stepDays - 1)->endOfDay();
            $periods->push($this->buildPeriod($start, $end, $frequency, $today));
        }

        return $periods;
    }

    /**
     * Build a specific period from a start-date key (YYYY-MM-DD).
     * Returns null if the key doesn't align to a valid period start for this frequency.
     */
    public function fromKey(InstructorProfile $profile, string $key): ?array
    {
        try {
            $start = Carbon::parse($key, self::TZ)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        $frequency = $this->normaliseFrequency($profile->payout_frequency ?? 'weekly');
        // Snap to nearest valid period start
        $aligned = $this->periodStartFor($start, $frequency);
        if (! $aligned->isSameDay($start)) {
            // Allow but snap to aligned start
            $start = $aligned;
        }
        $end = $start->copy()->addDays($this->stepDays($frequency) - 1)->endOfDay();
        $today = Carbon::now(self::TZ)->startOfDay();
        return $this->buildPeriod($start, $end, $frequency, $today);
    }

    // ── Internals ───────────────────────────────────────

    private function buildPeriod(Carbon $start, Carbon $end, string $frequency, Carbon $today): array
    {
        return [
            'key'        => $start->format('Y-m-d'),
            'start'      => $start->copy(),
            'end'        => $end->copy(),
            'label'      => $this->formatLabel($start, $end),
            'frequency'  => $frequency,
            'is_current' => $today->between($start->copy()->startOfDay(), $end->copy()->endOfDay()),
        ];
    }

    private function formatLabel(Carbon $start, Carbon $end): string
    {
        if ($start->isSameMonth($end)) {
            return $start->format('j') . ' – ' . $end->format('j M Y');
        }
        if ($start->isSameYear($end)) {
            return $start->format('j M') . ' – ' . $end->format('j M Y');
        }
        return $start->format('j M Y') . ' – ' . $end->format('j M Y');
    }

    private function stepDays(string $frequency): int
    {
        return match ($frequency) {
            'fortnightly'      => 14,
            'every_four_weeks' => 28,
            default            => 7,
        };
    }

    /**
     * The start (Sunday 00:00) of the period that contains $date for the given frequency.
     */
    private function periodStartFor(Carbon $date, string $frequency): Carbon
    {
        $date = $date->copy()->timezone(self::TZ)->startOfDay();
        $weekStart = $date->copy()->startOfWeek(Carbon::SUNDAY);

        if ($frequency === 'weekly') {
            return $weekStart;
        }

        $stepDays = $this->stepDays($frequency); // 14 or 28
        // Anchor: first Sunday of year 2000 (an arbitrary but stable reference)
        $anchor = Carbon::create(2000, 1, 2, 0, 0, 0, self::TZ); // Jan 2 2000 was Sunday
        $daysSinceAnchor = $anchor->diffInDays($weekStart, false);
        $remainder = ((int) $daysSinceAnchor) % $stepDays;
        return $weekStart->copy()->subDays($remainder);
    }

    private function normaliseFrequency(?string $f): string
    {
        $f = strtolower((string) $f);
        return in_array($f, ['weekly', 'fortnightly', 'every_four_weeks'], true) ? $f : 'weekly';
    }
}

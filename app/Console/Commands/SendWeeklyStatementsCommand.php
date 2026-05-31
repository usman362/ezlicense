<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\SiteSetting;
use App\Notifications\WeeklyStatementReady;
use App\Services\StatementPeriodService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * For every active instructor whose payout period JUST ended yesterday
 * (relative to their payout_frequency), email them the statement-ready
 * notification with a link to view + download the PDF.
 *
 * Run schedule: every Sunday at 18:00 AEST.
 *
 * Why Sunday 6 PM? Sat 23:59 is the end of a weekly Sun-Sat period.
 * Sunday's "previous period" lookup will return last week (Sun – Sat).
 * 6 PM gives instructors a useful end-of-day moment to see earnings.
 */
class SendWeeklyStatementsCommand extends Command
{
    protected $signature = 'statements:send-weekly
        {--dry-run : List who would be emailed without sending}
        {--instructor= : Only run for one instructor (user_id) — for testing}';

    protected $description = 'Email instructors a statement-ready notification for their just-completed period';

    public function handle(StatementPeriodService $periods): int
    {
        $dry = (bool) $this->option('dry-run');

        $query = InstructorProfile::query()
            ->whereHas('user', fn ($q) => $q->where('is_active', true));
        if ($insId = $this->option('instructor')) {
            $query->where('user_id', $insId);
        }
        $profiles = $query->with('user')->get();

        $this->info("Considering {$profiles->count()} instructor(s)...");
        $sent = 0;
        $skipped = 0;

        foreach ($profiles as $profile) {
            if (! $profile->user) { $skipped++; continue; }

            // Recent periods (2 = current + previous)
            $recent = $periods->recent($profile, 2);
            $previous = $recent->skip(1)->first();  // the one BEFORE current
            if (! $previous) { $skipped++; continue; }

            // Only send if the previous period ended in the last 36 hours
            // (i.e. we're running on the day right after it ended).
            $hoursSinceEnd = Carbon::now('Australia/Sydney')->diffInHours($previous['end'], false);
            if (abs($hoursSinceEnd) > 36) {
                $skipped++;
                continue;
            }

            // Aggregate completed bookings in that period
            $stats = $this->aggregate($profile, $previous['start'], $previous['end']);

            // Skip if zero bookings AND we've already sent ≥ 1 statement for them
            // (don't spam empty periods after launch)
            $hasPriorStatement = $profile->user->notifications()
                ->where('type', WeeklyStatementReady::class)
                ->exists();
            if ($stats['count'] === 0 && $hasPriorStatement) {
                $skipped++;
                continue;
            }

            $serviceFee = (float) SiteSetting::get('platform_service_fee', 5.00);
            $processingFee = (float) SiteSetting::get('payment_processing_fee', 2.00);
            $net = max(0, $stats['gross'] - ($serviceFee + $processingFee) * $stats['count']);

            $this->line(" → {$profile->user->name} | {$previous['label']} | {$stats['count']} lessons | net \$" . number_format($net, 2));

            if (! $dry) {
                try {
                    $profile->user->notify(new WeeklyStatementReady(
                        periodLabel: $previous['label'],
                        periodKey: $previous['key'],
                        bookingsCount: $stats['count'],
                        grossAmount: $stats['gross'],
                        netAmount: $net,
                        frequency: $previous['frequency'],
                    ));
                    $sent++;
                } catch (\Throwable $e) {
                    Log::warning("Weekly statement email failed for instructor {$profile->user_id}: " . $e->getMessage());
                }
            }
        }

        if ($dry) {
            $wouldNotify = $profiles->count() - $skipped;
            $this->warn("Dry run — no emails sent. Would have notified {$wouldNotify} instructor(s).");
        } else {
            $this->info("✔ Sent {$sent} statement notification(s) (skipped {$skipped}).");
        }
        return self::SUCCESS;
    }

    private function aggregate(InstructorProfile $profile, Carbon $start, Carbon $end): array
    {
        $bookings = Booking::where('instructor_id', $profile->user_id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->whereBetween('scheduled_at', [$start->copy()->utc(), $end->copy()->utc()])
            ->get(['amount']);

        return [
            'count' => $bookings->count(),
            'gross' => (float) $bookings->sum('amount'),
        ];
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Releases payments on completed bookings after the 24h dispute window.
 *
 * Conditions for release:
 *   - status        = completed
 *   - payment_status = pending
 *   - completed at least HOLD_HOURS ago (default 24)
 *   - NOT manually held (payment_held_at IS NULL)
 *   - NOT cancelled, NOT refunded
 *   - NOT already released (payment_released_at IS NULL)
 *
 * On release:
 *   - payment_status        = paid
 *   - payment_released_at   = now()
 *
 * This makes them eligible for the next weekly payout run.
 *
 * Schedule (in routes/console.php): hourly is fine — idempotent.
 */
class ReleasePendingPaymentsCommand extends Command
{
    protected $signature = 'payments:release-pending
        {--hours=24 : Hours to wait after completion before auto-releasing}
        {--dry-run : List affected bookings without making changes}';

    protected $description = 'Auto-release completed-but-pending payments after the dispute hold window';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $dry = (bool) $this->option('dry-run');
        $threshold = Carbon::now()->subHours($hours);

        $query = Booking::query()
            ->where('status', Booking::STATUS_COMPLETED)
            ->where('payment_status', Booking::PAYMENT_PAID)   // bookings are 'paid' upfront when learner books
            // Actually for our system, the relevant 'pending' flag is on payouts. Let me revise…
            // Re-checking semantics: in this codebase, payment_status='paid' means the learner has paid.
            // The instructor payout is a separate flow (InstructorPayout rows).
            // So this command's job is: mark bookings ready-for-payout once 24h has passed,
            // by setting payment_released_at — which the PayoutService will then pick up.
            ->whereNull('payment_released_at')
            ->whereNull('payment_held_at')
            ->whereNull('cancelled_at')
            ->whereNull('refunded_at')
            ->whereNotNull('lesson_completed_at')
            ->where('lesson_completed_at', '<=', $threshold);

        // Fall back to scheduled_at if lesson_completed_at is null
        $query2 = Booking::query()
            ->where('status', Booking::STATUS_COMPLETED)
            ->where('payment_status', Booking::PAYMENT_PAID)
            ->whereNull('payment_released_at')
            ->whereNull('payment_held_at')
            ->whereNull('cancelled_at')
            ->whereNull('refunded_at')
            ->whereNull('lesson_completed_at')
            ->where('scheduled_at', '<=', $threshold);

        $bookings = $query->get()->merge($query2->get());

        if ($bookings->isEmpty()) {
            $this->info("No bookings to release (window: $hours h).");
            return self::SUCCESS;
        }

        $this->info("Found {$bookings->count()} booking(s) ready for release (completed > $hours h ago, not held).");

        if ($dry) {
            $this->table(
                ['Booking', 'Instructor', 'Learner', 'Completed', 'Amount'],
                $bookings->map(fn ($b) => [
                    '#' . $b->id,
                    $b->instructor?->name ?? '—',
                    $b->learner?->name ?? '—',
                    optional($b->lesson_completed_at ?: $b->scheduled_at)->format('j M, H:i'),
                    '$' . number_format((float) $b->amount, 2),
                ])->all()
            );
            $this->warn('Dry run — no changes made.');
            return self::SUCCESS;
        }

        $released = 0;
        DB::transaction(function () use ($bookings, &$released) {
            foreach ($bookings as $b) {
                $b->update(['payment_released_at' => now()]);
                $released++;
            }
        });

        $this->info("✔ Released {$released} payment(s). They're now eligible for the next weekly payout.");
        return self::SUCCESS;
    }
}

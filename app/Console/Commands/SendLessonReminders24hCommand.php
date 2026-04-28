<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\LessonReminder24h;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Sends a 24-hour reminder to BOTH the learner and instructor for any
 * confirmed booking starting in the next 23-25 hour window.
 *
 * Runs hourly via the scheduler. Each booking is reminded only once
 * (tracked by `reminder_24h_sent_at`).
 */
class SendLessonReminders24hCommand extends Command
{
    protected $signature = 'lessons:remind-24h {--dry-run : Show what would be sent without sending}';

    protected $description = 'Send 24-hour lesson reminders to learners and instructors';

    public function handle(): int
    {
        $now = now();
        // Window: lessons starting between 23h and 25h from now (covers hourly cron drift)
        $windowStart = $now->copy()->addHours(23);
        $windowEnd = $now->copy()->addHours(25);

        $bookings = Booking::where('status', Booking::STATUS_CONFIRMED)
            ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
            ->whereNull('reminder_24h_sent_at')
            ->with(['learner', 'instructor', 'suburb.state'])
            ->get();

        $this->info("Found {$bookings->count()} booking(s) needing 24-hour reminders.");

        $dryRun = $this->option('dry-run');
        $sent = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            $when = $booking->scheduled_at->format('D j M, g:i a');
            $this->line("→ Booking #{$booking->id} ({$when})");

            if ($dryRun) {
                $this->comment('  [dry-run] would notify learner + instructor');
                continue;
            }

            try {
                if ($booking->learner) {
                    $booking->learner->notify(new LessonReminder24h($booking, 'learner'));
                }
                if ($booking->instructor) {
                    $booking->instructor->notify(new LessonReminder24h($booking, 'instructor'));
                }

                $booking->update(['reminder_24h_sent_at' => $now]);
                $sent++;
                $this->info('  ✓ sent');
            } catch (\Throwable $e) {
                Log::warning("Lesson reminder failed for booking #{$booking->id}: " . $e->getMessage());
                $failed++;
                $this->error('  ✗ failed: ' . $e->getMessage());
            }
        }

        $this->info("Done. Sent: {$sent}, Failed: {$failed}, Total processed: {$bookings->count()}");
        return Command::SUCCESS;
    }
}

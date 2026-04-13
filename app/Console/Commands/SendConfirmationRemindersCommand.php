<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\LessonConfirmationRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendConfirmationRemindersCommand extends Command
{
    protected $signature = 'confirmations:remind
                            {--hours=4 : Hours after initial send before first reminder}
                            {--max-reminders=3 : Maximum reminders to send per booking}';

    protected $description = 'Send reminders to learners who have not confirmed completed lessons';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $maxReminders = (int) $this->option('max-reminders');

        // Find completed bookings where:
        // - Confirmation was sent but not yet confirmed
        // - Enough time has passed since last send/reminder
        // - Haven't exceeded max reminders
        $bookings = Booking::where('status', Booking::STATUS_COMPLETED)
            ->whereNotNull('confirmation_sent_at')
            ->whereNull('learner_confirmed_at')
            ->where('confirmation_reminder_count', '<', $maxReminders)
            ->where(function ($q) use ($hours) {
                // Either: never reminded AND sent > N hours ago
                $q->where(function ($q2) use ($hours) {
                    $q2->whereNull('confirmation_reminded_at')
                       ->where('confirmation_sent_at', '<=', now()->subHours($hours));
                })
                // Or: last reminder was > N hours ago
                ->orWhere(function ($q2) use ($hours) {
                    $q2->whereNotNull('confirmation_reminded_at')
                       ->where('confirmation_reminded_at', '<=', now()->subHours($hours));
                });
            })
            ->with(['learner', 'instructor'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No pending confirmation reminders to send.');
            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            $learner = $booking->learner;
            if (!$learner) {
                continue;
            }

            try {
                $learner->notify(new LessonConfirmationRequest($booking, isReminder: true));

                $booking->update([
                    'confirmation_reminded_at' => now(),
                    'confirmation_reminder_count' => $booking->confirmation_reminder_count + 1,
                ]);

                $sent++;

                Log::info('Confirmation reminder sent', [
                    'booking_id' => $booking->id,
                    'learner_id' => $learner->id,
                    'reminder_number' => $booking->confirmation_reminder_count,
                ]);
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('Confirmation reminder failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Confirmation reminders: {$sent} sent, {$failed} failed.");

        return self::SUCCESS;
    }
}

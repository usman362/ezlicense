<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Traits\SendsSms;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

/**
 * 24-hour lesson reminder. Sent to BOTH learner and instructor a day before
 * the scheduled lesson. Reduces no-shows + lets either party reschedule in time.
 *
 * Sent by `lessons:remind-24h` scheduled command (runs hourly).
 */
class LessonReminder24h extends Notification
{
    use Queueable, SendsSms;

    public function __construct(
        public Booking $booking,
        public string $audience = 'learner', // 'learner' or 'instructor'
    ) {
    }

    public function via($notifiable): array
    {
        return array_merge(['database', 'mail'], $this->smsChannel($notifiable));
    }

    public function toMail($notifiable): MailMessage
    {
        $b = $this->booking->loadMissing(['learner:id,name,phone', 'instructor:id,name,phone', 'suburb.state']);
        $isLearner = $this->audience === 'learner';

        $location = $b->suburb
            ? trim($b->suburb->name . ' ' . ($b->suburb->postcode ?? '') . ' ' . ($b->suburb->state?->code ?? ''))
            : 'TBD';

        $when = $b->scheduled_at;
        $whenLabel = $when->format('l, j F Y') . ' at ' . $when->format('g:i a');
        $type = $b->type === 'test_package' ? 'Driving Test Package' : ($b->duration_minutes >= 120 ? '2-Hour Lesson' : '1-Hour Lesson');

        $msg = (new MailMessage)
            ->subject('Reminder: Your lesson is tomorrow — ' . $when->format('j M, g:i a'));

        if ($isLearner) {
            $instructorName = $b->instructor?->name ?? 'your instructor';
            $instructorPhone = $b->instructor?->phone;

            $msg->greeting('Hi ' . $notifiable->name . ',')
                ->line('Just a friendly reminder — you have a driving lesson **tomorrow**:')
                ->line('📅 **' . $whenLabel . '**')
                ->line('🚗 ' . $type)
                ->line('👤 With **' . $instructorName . '**' . ($instructorPhone ? ' (' . $instructorPhone . ')' : ''))
                ->line('📍 Pick-up: **' . $location . '**')
                ->action('View Booking', url('/learner/dashboard'))
                ->line('**Tips for your lesson:**')
                ->line('• Bring your learner permit')
                ->line('• Wear comfortable closed shoes')
                ->line('• Be ready 5 minutes before pick-up')
                ->line('Need to cancel or reschedule? Please do so at least 24 hours in advance to avoid fees.')
                ->salutation('See you tomorrow! — The Secure Licences Team');
        } else {
            // Instructor view
            $learnerName = $b->learner?->name ?? 'your learner';
            $learnerPhone = $b->learner?->phone;

            $msg->greeting('Hi ' . $notifiable->name . ',')
                ->line('Reminder — you have a lesson scheduled for **tomorrow**:')
                ->line('📅 **' . $whenLabel . '**')
                ->line('👤 Learner: **' . $learnerName . '**' . ($learnerPhone ? ' (' . $learnerPhone . ')' : ''))
                ->line('🚗 ' . $type . ($b->transmission ? ' (' . ucfirst($b->transmission) . ')' : ''))
                ->line('📍 Pick-up: **' . $location . '**')
                ->action('View in Calendar', url('/instructor/calendar'))
                ->line('Please confirm your vehicle is ready and contact the learner if there are any changes.')
                ->salutation('— The Secure Licences Team');
        }

        return $msg;
    }

    public function toVonage($notifiable): VonageMessage
    {
        $b = $this->booking;
        $time = $b->scheduled_at->format('j M, g:i a');
        $isLearner = $this->audience === 'learner';

        if ($isLearner) {
            $instructor = $b->instructor?->name ?? 'your instructor';
            $content = "SecureLicences: Reminder — your driving lesson with {$instructor} is tomorrow at {$time}.";
        } else {
            $learner = $b->learner?->name ?? 'your learner';
            $content = "SecureLicences: Reminder — lesson with {$learner} tomorrow at {$time}.";
        }

        return (new VonageMessage)->content($content);
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'lesson_reminder_24h',
            'title' => 'Lesson tomorrow',
            'body' => 'Your lesson is at ' . $this->booking->scheduled_at->format('g:i a') . ' tomorrow.',
            'booking_id' => $this->booking->id,
            'scheduled_at' => $this->booking->scheduled_at->toIso8601String(),
        ];
    }
}

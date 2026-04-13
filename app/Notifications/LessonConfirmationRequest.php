<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use App\Traits\SendsSms;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class LessonConfirmationRequest extends Notification
{
    use Queueable, SendsSms;

    public function __construct(
        protected Booking $booking,
        protected bool $isReminder = false
    ) {}

    public function via(object $notifiable): array
    {
        return array_merge(['database', 'mail'], $this->smsChannel($notifiable));
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('d M, g:i A') : 'recent';
        $url = $b->getConfirmationUrl();

        $prefix = $this->isReminder ? 'Reminder: ' : '';

        return (new VonageMessage)
            ->content("{$prefix}SecureLicences: Please confirm your lesson on {$date} was completed. Tap here: {$url}");
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'recently';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : '';
        $type = $b->type === 'test_package' ? 'Test Package' : 'Driving Lesson';
        $instructorName = $b->instructor->name ?? 'your instructor';

        $subject = $this->isReminder
            ? "Reminder: Please confirm your {$type} was completed"
            : "Please confirm your {$type} was completed";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hi {$notifiable->first_name}!");

        if ($this->isReminder) {
            $message->line("Just a friendly reminder — we still need your confirmation for the lesson below.");
        }

        $message
            ->line("Your **{$type}** with **{$instructorName}** on **{$date}** at **{$time}** has been marked as completed.")
            ->line("Please confirm that you received this lesson by clicking the button below. This helps us keep accurate records and ensure quality service.")
            ->action('Yes, I Completed This Lesson', $b->getConfirmationUrl())
            ->line("**Why do we ask?** Your confirmation is our proof that the service was delivered. This protects both you and your instructor.")
            ->line("If there was an issue with your lesson, please contact us at support@securelicences.com.au instead of clicking the button.")
            ->salutation("Thanks,\nThe {$siteName} Team");

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        $b = $this->booking;
        $type = $b->type === 'test_package' ? 'test package' : 'lesson';

        return [
            'booking_id' => $b->id,
            'type' => $this->isReminder ? 'lesson_confirmation_reminder' : 'lesson_confirmation_request',
            'message' => $this->isReminder
                ? "Reminder: Please confirm your {$type} on " . ($b->scheduled_at ? $b->scheduled_at->format('d M Y') : 'recent date') . ' was completed.'
                : "Please confirm your {$type} on " . ($b->scheduled_at ? $b->scheduled_at->format('d M Y') : 'recent date') . ' was completed.',
            'confirmation_url' => $b->getConfirmationUrl(),
        ];
    }
}

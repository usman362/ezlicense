<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use App\Traits\SendsSms;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the instructor when a learner books with them.
 */
class InstructorNewBooking extends Notification
{
    use Queueable, SendsSms;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return array_merge(['database', 'mail'], $this->smsChannel($notifiable));
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $b = $this->booking;
        $b->loadMissing('learner:id,name');
        $learner = $b->learner->name ?? 'A learner';
        $date = $b->scheduled_at ? $b->scheduled_at->format('D d M, g:i A') : 'TBC';

        return (new VonageMessage)
            ->content("SecureLicences: New booking! {$learner} booked a lesson on {$date}. View at " . url('/instructor/calendar'));
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $b = $this->booking;
        $b->loadMissing(['learner:id,name,email,phone', 'suburb.state']);

        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'TBC';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : 'TBC';
        $type = $b->type === 'test_package' ? 'Test Package' : 'Driving Lesson';
        $learnerName = $b->learner->name ?? 'A learner';
        $learnerPhone = $b->learner->phone ?? '';
        $amount = number_format((float) $b->amount, 2);
        $location = '';
        if ($b->suburb) {
            $parts = array_filter([$b->suburb->name, $b->suburb->postcode, $b->suburb->state?->code]);
            $location = implode(' ', $parts);
        }

        $message = (new MailMessage)
            ->subject("New Booking! {$learnerName} — {$type} on {$date}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("Great news! You have a new booking on {$siteName}.")
            ->line("**{$type}**")
            ->line("**Learner:** {$learnerName}")
            ->line("**Date:** {$date} at {$time}")
            ->line("**Duration:** {$b->duration_minutes} minutes")
            ->line("**Amount:** \${$amount} AUD");

        if ($location) {
            $message->line("**Location:** {$location}");
        }

        if ($learnerPhone) {
            $message->line("**Learner Phone:** {$learnerPhone}");
        }

        if ($b->learner_notes) {
            $message->line("**Learner Notes:** {$b->learner_notes}");
        }

        $message
            ->action('View Your Calendar', url('/instructor/calendar'))
            ->line('Please make sure to arrive on time and be prepared for your learner.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        $this->booking->loadMissing('learner:id,name');
        $learnerName = $this->booking->learner->name ?? 'A learner';

        return [
            'booking_id' => $this->booking->id,
            'type' => 'new_booking',
            'message' => "New booking from {$learnerName} on " . ($this->booking->scheduled_at?->format('D, d M') ?? 'TBC'),
            'scheduled_at' => $this->booking->scheduled_at?->toIso8601String(),
        ];
    }
}

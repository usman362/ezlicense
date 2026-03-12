<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingProposed extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $instructorName = ''
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        $smtpHost = SiteSetting::get('smtp_host');
        if (! empty($smtpHost)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'EzLicence');
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'TBC';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : 'TBC';

        return (new MailMessage)
            ->subject("New Booking Proposal from {$this->instructorName}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("{$this->instructorName} has proposed a booking for you.")
            ->line("**{$date}** at **{$time}** ({$b->duration_minutes} minutes)")
            ->line("This proposal expires in 24 hours.")
            ->action('View Proposal', url('/learner/dashboard'))
            ->line("Log in to accept or decline this proposal.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => 'booking_proposed',
            'message' => "{$this->instructorName} proposed a booking for you.",
            'scheduled_at' => $this->booking->scheduled_at?->toIso8601String(),
        ];
    }
}

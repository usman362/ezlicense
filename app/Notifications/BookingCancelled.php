<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $reason = ''
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

        $mail = (new MailMessage)
            ->subject("Booking Cancelled — #{$b->id}")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Your booking #{$b->id} on **{$date}** has been cancelled.");

        if ($this->reason) {
            $mail->line("Reason: {$this->reason}");
        }

        return $mail
            ->action('View My Bookings', url('/learner/dashboard'))
            ->line("If you have questions, please contact support.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => 'booking_cancelled',
            'message' => 'Booking #' . $this->booking->id . ' has been cancelled.',
            'reason' => $this->reason,
        ];
    }
}

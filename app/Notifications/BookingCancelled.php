<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use App\Traits\SendsSms;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification
{
    use Queueable, SendsSms;

    public function __construct(
        protected Booking $booking,
        protected string $reason = '',
        protected string $message = ''
    ) {}

    public function via(object $notifiable): array
    {
        return array_merge(['database', 'mail'], $this->smsChannel($notifiable));
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('D d M, g:i A') : '';

        return (new VonageMessage)
            ->content("SecureLicences: Booking #{$b->id} on {$date} has been cancelled. Reason: " . ($this->reason ?: 'Not specified') . ". Log in for details.");
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'TBC';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : 'TBC';

        $mail = (new MailMessage)
            ->subject("Booking Cancelled — #{$b->id}")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Your booking #{$b->id} on **{$date}** at **{$time}** has been cancelled.");

        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }

        // Include the personal message from the cancelling party
        if ($this->message || $b->cancellation_message) {
            $msg = $this->message ?: $b->cancellation_message;
            $mail->line("**Message:** {$msg}");
        }

        // If rescheduled, let learner know
        if ($b->rescheduledToBooking) {
            $newDate = $b->rescheduledToBooking->scheduled_at?->format('l, d M Y');
            $newTime = $b->rescheduledToBooking->scheduled_at?->format('g:i A');
            $mail->line("A new booking has been proposed for **{$newDate}** at **{$newTime}**. Please log in to accept or decline.");
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
            'cancellation_message' => $this->message ?: $this->booking->cancellation_message,
            'rescheduled_to_booking_id' => $this->booking->rescheduledToBooking?->id,
        ];
    }
}

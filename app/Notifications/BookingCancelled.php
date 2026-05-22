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
        $siteName = SiteSetting::get('site_name', 'Secure Licence');
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

        // ── Refund breakdown (when a refund has been issued with the cancellation) ──
        if ($b->refund_amount !== null && (float) $b->refund_amount > 0) {
            $original     = number_format((float) $b->amount, 2);
            $refunded     = number_format((float) $b->refund_amount, 2);
            $cancelFee    = max(0, (float) $b->amount - (float) $b->refund_amount);
            $cancelFeeFmt = number_format($cancelFee, 2);
            $methodLabel  = match ($b->refund_method) {
                'wallet'           => 'Secure Licence wallet credit',
                'original_payment' => 'Original payment method (card)',
                'manual_bank'      => 'Bank transfer',
                default            => 'Refund processed',
            };
            $timeline = match ($b->refund_method) {
                'wallet'           => 'Credit is available in your wallet right now.',
                'original_payment' => 'Card refunds typically take 5–10 business days to show on your statement.',
                'manual_bank'      => 'Bank transfers usually clear within 1–3 business days.',
                default            => 'You should see this refund shortly.',
            };

            $mail->line('**Refund summary**')
                 ->line("Original amount paid: \${$original}");
            if ($cancelFee > 0) {
                $mail->line("Cancellation fee retained: −\${$cancelFeeFmt}");
            }
            $mail->line("**Refunded: \${$refunded}**")
                 ->line("Refund method: {$methodLabel}")
                 ->line($timeline);
        } elseif ($b->refund_amount !== null && (float) $b->refund_amount === 0.0) {
            // Explicit no-refund (e.g. late cancellation, fee = 100%)
            $original = number_format((float) $b->amount, 2);
            $mail->line('**Refund summary**')
                 ->line("Original amount paid: \${$original}")
                 ->line('Per our cancellation policy, no refund is being issued for this booking.');
            if ($b->refund_reason) {
                $mail->line("Note: {$b->refund_reason}");
            }
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

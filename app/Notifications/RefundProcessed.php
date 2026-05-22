<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundProcessed extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $b = $this->booking->loadMissing(['learner', 'instructor', 'suburb']);

        $amount      = number_format((float) $b->refund_amount, 2);
        $original    = number_format((float) $b->amount, 2);
        $cancelFee   = max(0, (float) $b->amount - (float) $b->refund_amount);
        $cancelFeeFmt= number_format($cancelFee, 2);
        $date        = optional($b->scheduled_at)->format('l, j F Y');
        $time        = optional($b->scheduled_at)->format('g:i A');
        $methodLabel = match ($b->refund_method) {
            'wallet'           => 'Secure Licence wallet credit',
            'original_payment' => 'Original payment method (card)',
            'manual_bank'      => 'Bank transfer',
            default            => 'Refund processed',
        };
        $timelineNote = match ($b->refund_method) {
            'wallet'           => 'Credit is available in your wallet right now — use it on your next booking.',
            'original_payment' => 'Card refunds typically take 5–10 business days to appear on your statement.',
            'manual_bank'      => 'Bank transfers usually clear within 1–3 business days.',
            default            => 'You should see this refund shortly.',
        };

        $msg = (new MailMessage)
            ->subject('Refund processed for booking #' . $b->id . ' — $' . $amount)
            ->greeting('Hi ' . ($b->learner->first_name ?? $b->learner->name ?? 'there') . ',')
            ->line('We\'ve processed a refund of **$' . $amount . '** for your booking with us. Full details below.');

        // Receipt block
        $msg->line('**Refund summary**')
            ->line('Booking reference: #' . $b->id)
            ->line('Lesson date: ' . ($date ?: '—') . ($time ? ' at ' . $time : ''))
            ->line('Instructor: ' . ($b->instructor->name ?? '—'))
            ->line('Original amount paid: $' . $original);

        if ($cancelFee > 0) {
            $msg->line('Cancellation fee retained: −$' . $cancelFeeFmt);
        }

        $msg->line('**Refund amount: $' . $amount . '**')
            ->line('Refund method: ' . $methodLabel);

        if ($b->refund_reason) {
            $msg->line('Reason: ' . $b->refund_reason);
        }
        if ($b->refund_reference) {
            $msg->line('Reference: ' . $b->refund_reference);
        }

        $msg->line($timelineNote);

        if ($b->refund_method === 'wallet') {
            $msg->action('View wallet balance', url('/learner/wallet'));
        } else {
            $msg->action('View booking history', url('/learner/dashboard'));
        }

        return $msg
            ->line('Questions about this refund? Just reply to this email or contact our team at support@securelicence.com.')
            ->salutation('Thanks — The Secure Licence team');
    }
}

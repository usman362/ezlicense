<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to learner immediately after successful payment.
 * Includes a receipt-style breakdown (amount, fees, GST) for their records.
 */
class PaymentReceipt extends Notification
{
    use Queueable;

    /**
     * @param  array<int, Booking>  $bookings  All bookings paid for in this transaction
     * @param  float  $totalCharged  Total amount charged to the learner
     * @param  string  $paymentMethod  card / paypal / wallet
     * @param  string|null  $transactionRef  Gateway transaction reference (if any)
     */
    public function __construct(
        public array $bookings,
        public float $totalCharged,
        public string $paymentMethod = 'card',
        public ?string $transactionRef = null,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $first = $this->bookings[0] ?? null;
        $count = count($this->bookings);
        $instructor = $first?->instructor?->name ?? 'your instructor';
        $receiptNumber = 'SL-' . now()->format('Ymd') . '-' . str_pad((string) ($first?->id ?? '0'), 6, '0', STR_PAD_LEFT);

        $msg = (new MailMessage)
            ->subject('Payment Receipt — Secure Licences #' . $receiptNumber)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Thanks for your payment! Here\'s your receipt for ' . $count . ' booking' . ($count > 1 ? 's' : '') . ' with **' . $instructor . '**.');

        // Booking summary lines
        foreach ($this->bookings as $b) {
            $b->loadMissing(['suburb.state']);
            $location = $b->suburb ? $b->suburb->name . ' ' . ($b->suburb->postcode ?? '') : '';
            $msg->line('• **' . $b->scheduled_at->format('D, j M Y') . ' at ' . $b->scheduled_at->format('g:i a') . '** — '
                . ($b->type === 'test_package' ? 'Test Package' : 'Driving Lesson')
                . ($location ? ' (' . trim($location) . ')' : '')
                . ' — $' . number_format((float) $b->amount, 2));
        }

        return $msg
            ->line('---')
            ->line('**Receipt number:** ' . $receiptNumber)
            ->line('**Payment method:** ' . ucfirst($this->paymentMethod))
            ->when($this->transactionRef, fn ($m) => $m->line('**Transaction reference:** ' . $this->transactionRef))
            ->line('**Total charged:** $' . number_format($this->totalCharged, 2))
            ->action('View My Bookings', url('/learner/dashboard'))
            ->line('Keep this email for your records. If you have any questions about this charge, just reply or contact our support team.')
            ->salutation('The Secure Licences Team');
    }

    public function toArray($notifiable): array
    {
        $count = count($this->bookings);
        return [
            'type' => 'payment_receipt',
            'title' => 'Payment received',
            'body' => 'Your payment of $' . number_format($this->totalCharged, 2) . ' for ' . $count . ' booking' . ($count > 1 ? 's' : '') . ' was successful.',
            'amount' => $this->totalCharged,
            'booking_ids' => array_map(fn ($b) => $b->id, $this->bookings),
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\InstructorPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutProcessed extends Notification
{
    use Queueable;

    public function __construct(public InstructorPayout $payout) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your payout of $' . number_format($this->payout->net_amount, 2) . ' has been processed')
            ->greeting('Hi ' . ($notifiable->first_name ?? $notifiable->name) . ',')
            ->line('Your weekly payout has been processed and sent to your bank account.')
            ->line('**Payout reference:** ' . $this->payout->reference)
            ->line('**Period:** ' . $this->payout->periodLabel())
            ->line('**Bookings:** ' . $this->payout->bookings_count)
            ->line('**Gross earnings:** $' . number_format($this->payout->gross_amount, 2))
            ->line('**Platform fees:** -$' . number_format($this->payout->totalDeductions(), 2))
            ->line('**Net payout:** $' . number_format($this->payout->net_amount, 2))
            ->action('View Your Earnings', url('/instructor/reports'))
            ->line('Payments typically arrive within 1–2 business days depending on your bank.');
    }

    public function toArray($notifiable): array
    {
        return [
            'payout_id'  => $this->payout->id,
            'reference'  => $this->payout->reference,
            'net_amount' => (float) $this->payout->net_amount,
            'period'     => $this->payout->periodLabel(),
            'message'    => 'Payout of $' . number_format($this->payout->net_amount, 2) . ' processed for ' . $this->payout->periodLabel(),
        ];
    }
}

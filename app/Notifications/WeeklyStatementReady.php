<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to an instructor when their statement for the previous period is ready
 * to view + download. Delivered by the SendWeeklyStatementsCommand.
 */
class WeeklyStatementReady extends Notification
{
    use Queueable;

    public function __construct(
        public string $periodLabel,           // e.g. "10 – 16 May 2026"
        public string $periodKey,             // e.g. "2026-05-10" — for URL
        public int $bookingsCount,
        public float $grossAmount,
        public float $netAmount,
        public string $frequency,             // weekly|fortnightly|every_four_weeks
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $viewUrl = url('/instructor/statements/' . $this->periodKey);
        $pdfUrl = url('/instructor/statements/' . $this->periodKey . '/download');
        $freq = ucfirst(str_replace('_', ' ', $this->frequency));

        $mail = (new MailMessage)
            ->subject("Your {$freq} Statement Is Ready — \${$this->netAmount}")
            ->greeting('Hi ' . ($notifiable->first_name ?? $notifiable->name ?? 'there') . ',')
            ->line("Your **{$this->periodLabel}** statement is now available.");

        if ($this->bookingsCount > 0) {
            $mail->line('**Summary**')
                ->line('• Lessons delivered: **' . $this->bookingsCount . '**')
                ->line('• Gross earned: **$' . number_format($this->grossAmount, 2) . '**')
                ->line('• Net payout: **$' . number_format($this->netAmount, 2) . '**');
            $mail->line('Your payout will be processed on the next scheduled run and should land in your nominated bank account within 1–3 business days.');
        } else {
            $mail->line('No lessons were completed in this period, so there\'s no payout this time. Your statement is still available if you\'d like to review.');
        }

        return $mail
            ->action('View Statement', $viewUrl)
            ->line('You can also [download a PDF copy](' . $pdfUrl . ') for your records.')
            ->salutation('Thanks — The Secure Licence team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'           => 'weekly_statement_ready',
            'title'          => 'Statement ready: ' . $this->periodLabel,
            'body'           => $this->bookingsCount . ' lessons · $' . number_format($this->netAmount, 2) . ' net payout',
            'period_key'     => $this->periodKey,
            'period_label'   => $this->periodLabel,
            'frequency'      => $this->frequency,
            'bookings_count' => $this->bookingsCount,
            'gross_amount'   => $this->grossAmount,
            'net_amount'     => $this->netAmount,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletCredited extends Notification
{
    use Queueable;

    public function __construct(
        protected float $creditAmount,
        protected float $newBalance
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');

        return (new MailMessage)
            ->subject('Wallet Top-up Successful')
            ->greeting("Hi {$notifiable->first_name}!")
            ->line('Your wallet has been topped up with **$' . number_format($this->creditAmount, 2) . '**.')
            ->line('New balance: **$' . number_format($this->newBalance, 2) . '**')
            ->action('View Wallet', url('/learner/wallet'))
            ->line("Thank you for choosing {$siteName}!");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'wallet_credited',
            'message' => 'Wallet topped up with $' . number_format($this->creditAmount, 2),
            'new_balance' => $this->newBalance,
        ];
    }
}

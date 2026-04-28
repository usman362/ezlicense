<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Branded password reset email — replaces Laravel's default unbranded one.
 */
class PasswordResetNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your Secure Licences password')
            ->greeting('Hi ' . ($notifiable->name ?? 'there') . ',')
            ->line('We received a request to reset the password for your Secure Licences account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in **' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes**.')
            ->line('If you didn\'t request a password reset, you can safely ignore this email — your password will stay the same.')
            ->salutation('— The Secure Licences Team');
    }
}

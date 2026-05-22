<?php

namespace App\Notifications;

use App\Models\InstructorInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorInviteNotification extends Notification
{
    use Queueable;

    public function __construct(public InstructorInvite $invite)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->invite->url();

        // Round up to whole days so we never show "6.99 days".
        $daysLeft = (int) ceil(now()->diffInDays($this->invite->expires_at, false));
        $daysLeft = max(1, $daysLeft);
        $expiryLabel = $daysLeft === 1 ? '1 day' : ($daysLeft === 7 ? '1 week' : $daysLeft . ' days');

        // Inviter name — but skip generic role names like "Admin"
        $rawName = $this->invite->inviter?->name ?? null;
        $genericNames = ['admin', 'administrator', 'system', ''];
        $inviterName = ($rawName && ! in_array(strtolower(trim($rawName)), $genericNames, true))
            ? $rawName
            : 'The Secure Licence team';

        $msg = (new MailMessage)
            ->subject("You're invited to join Secure Licence as a driving instructor")
            ->greeting('Hi ' . ($this->invite->first_name ?: 'there') . ',')
            ->line($inviterName . ' has invited you to join Secure Licence as a verified driving instructor — Australia\'s fastest-growing platform for driving schools.');

        if ($this->invite->personal_note) {
            $msg->line('**A personal note for you:**')
                ->line('"' . $this->invite->personal_note . '"');
        }

        return $msg
            ->line('Click the button below to set your password and start your account. After that you\'ll upload your driving instructor licence, WWCC and insurance — most instructors finish in under 10 minutes.')
            ->action('Accept invitation', $url)
            ->line('**This link is just for you.** It expires in ' . $expiryLabel . ' and can only be used once, so please don\'t forward it on.')
            ->line('Questions? Just reply to this email, or reach out to our team at instructors@securelicence.com.')
            ->salutation('Welcome aboard 👋  — The Secure Licence team');
    }
}

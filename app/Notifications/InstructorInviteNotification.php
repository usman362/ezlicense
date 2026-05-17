<?php

namespace App\Notifications;

use App\Models\InstructorInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** Retry up to 3 times if SMTP fails (transient errors) */
    public $tries = 3;

    /** Hard cap each attempt at 30 seconds — prevents stuck jobs */
    public $timeout = 30;

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
        $expiresIn = now()->diffInDays($this->invite->expires_at);
        $inviterName = $this->invite->inviter?->name ?? 'The Secure Licences team';

        $msg = (new MailMessage)
            ->subject("You're invited to join Secure Licences as a driving instructor")
            ->greeting('Hi ' . ($this->invite->first_name ?: 'there') . ',')
            ->line($inviterName . ' has invited you to join Secure Licences as a verified driving instructor.');

        if ($this->invite->personal_note) {
            $msg->line('A note from ' . $inviterName . ':')
                ->line('"' . $this->invite->personal_note . '"');
        }

        return $msg
            ->line('Click the button below to set your password and start the verification process. You\'ll need to upload your driving instructor licence, WWCC and insurance documents — most instructors finish in under 10 minutes.')
            ->action('Accept invitation', $url)
            ->line('This link will expire in ' . $expiresIn . ' ' . str('day')->plural($expiresIn) . '. It can only be used by you — please don\'t forward it.')
            ->line('Questions? Reply to this email or contact our team at instructors@securelicences.com.au.')
            ->salutation('Welcome aboard, The Secure Licences team');
    }
}

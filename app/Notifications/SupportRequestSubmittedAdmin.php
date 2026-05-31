<?php

namespace App\Notifications;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportRequestSubmittedAdmin extends Notification
{
    use Queueable;

    public function __construct(public SupportRequest $request) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $r = $this->request;
        $msg = (new MailMessage)
            ->subject("[Support] {$r->reference} — {$r->subject}")
            ->greeting('New support request')
            ->line("**Reference:** {$r->reference}")
            ->line("**From:** {$r->name} <{$r->email}>" . ($r->phone ? " · {$r->phone}" : ''))
            ->line("**Role:** " . ($r->role ?: 'unspecified'))
            ->line("**Topic:** {$r->topic}")
            ->line("**Subject:** {$r->subject}")
            ->line('---')
            ->line($r->message)
            ->line('---')
            ->action('View in admin', url('/admin/support/requests/' . $r->id));

        return $msg;
    }
}

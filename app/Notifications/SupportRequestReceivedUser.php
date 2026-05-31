<?php

namespace App\Notifications;

use App\Models\SupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportRequestReceivedUser extends Notification
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
        return (new MailMessage)
            ->subject("We've received your request — {$r->reference}")
            ->greeting("Hi {$r->name},")
            ->line("Thanks for reaching out to Secure Licence Support. We've received your message and our team will respond within 1 business day.")
            ->line("**Your reference number:** {$r->reference}")
            ->line("**Subject:** {$r->subject}")
            ->line('---')
            ->line('**Your message:**')
            ->line($r->message)
            ->line('---')
            ->line('Please keep this reference number for your records — quote it in any follow-up emails so we can find your case quickly.')
            ->salutation('Thanks — The Secure Licence Support team');
    }
}

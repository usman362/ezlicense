<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorDocumentsSubmittedAdmin extends Notification
{
    use Queueable;

    public function __construct(public User $instructor) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $this->instructor->name ?: $this->instructor->email;

        return (new MailMessage)
            ->subject("[Verification] {$name} submitted their documents")
            ->greeting('Documents ready for review')
            ->line("**Instructor:** {$name}")
            ->line("**Email:** {$this->instructor->email}")
            ->line('They have uploaded their compliance documents and are now awaiting verification.')
            ->action('Review documents', url('/admin/instructors/' . optional($this->instructor->instructorProfile)->id))
            ->line('Approve to give them full access, or reject with a reason so they can re-submit.');
    }
}

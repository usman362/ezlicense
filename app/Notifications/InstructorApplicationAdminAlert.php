<?php

namespace App\Notifications;

use App\Models\InstructorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorApplicationAdminAlert extends Notification
{
    use Queueable;

    public function __construct(public InstructorApplication $application) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $a = $this->application;
        $docCount = is_array($a->documents) ? count($a->documents) : 0;
        return (new MailMessage)
            ->subject("[Instructor application] {$a->reference} — {$a->fullName()}")
            ->greeting('New instructor application')
            ->line("**Reference:** {$a->reference}")
            ->line("**Name:** {$a->fullName()}")
            ->line("**Email:** {$a->email}")
            ->line("**Phone:** {$a->phone}")
            ->line("**Experience:** " . ($a->years_experience !== null ? $a->years_experience . ' years' : '—'))
            ->line("**Transmission:** " . ucfirst($a->transmission ?? '—'))
            ->line("**Documents uploaded:** {$docCount}")
            ->action('Review application', url('/admin/instructor-applications/' . $a->id));
    }
}

<?php

namespace App\Notifications;

use App\Models\InstructorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorApplicationRejected extends Notification
{
    use Queueable;

    public function __construct(public InstructorApplication $application) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $a = $this->application;
        $msg = (new MailMessage)
            ->subject("Update on your Secure Licence application — {$a->reference}")
            ->greeting("Hi {$a->first_name},")
            ->line("Thanks for your interest in joining Secure Licence as an instructor.")
            ->line("After reviewing your application, we're unable to approve it at this time.");

        if ($a->rejection_reason) {
            $msg->line('**Reason:** ' . $a->rejection_reason);
        }

        return $msg
            ->line("If your situation changes (for example, new certifications or licensing), you're welcome to apply again in the future.")
            ->line('Questions about this decision? Reply to this email or contact support@securelicence.com.')
            ->salutation('Thanks — The Secure Licence team');
    }
}

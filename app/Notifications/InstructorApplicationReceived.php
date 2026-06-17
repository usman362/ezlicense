<?php

namespace App\Notifications;

use App\Models\InstructorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(public InstructorApplication $application) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $a = $this->application;
        return (new MailMessage)
            ->subject("Application received — {$a->reference}")
            ->greeting("Hi {$a->first_name},")
            ->line("Thanks for applying to become a Secure Licence instructor.")
            ->line("**Your reference:** {$a->reference}")
            ->line("Our team will review your documents within **2 business days**. You'll receive another email with the outcome.")
            ->line("**What happens next?**")
            ->line('1. We verify your driver licence and instructor certificate against Australian standards.')
            ->line('2. If approved, you\'ll get a one-click setup link to choose your password and complete your profile.')
            ->line('3. Once your profile is live, learners can book lessons with you.')
            ->line('If we need any extra information, we\'ll reach out by email or phone.')
            ->salutation('Thanks — The Secure Licence team');
    }
}

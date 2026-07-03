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
            ->subject("We've received your application — {$a->reference}")
            ->greeting("Hi {$a->first_name},")
            ->line("Thank you for applying to become a Secure Licence driving instructor. We've received your application and our team will review it within **2 business days**.")
            ->line("**Your reference:** {$a->reference}")
            ->line("**What happens next**")
            ->line('1. Our team reviews your application.')
            ->line('2. If your application is approved, we\'ll email you a secure link to set your password and **submit your documents** — your driver\'s licence, driving instructor licence and Working with Children Check (WWCC).')
            ->line('3. Once we\'ve reviewed and verified your documents, your profile goes live and learners in your area can start booking lessons with you.')
            ->line('There\'s nothing you need to do right now — we\'ll be in touch by email with the outcome. If we need any further information, we\'ll contact you by email or phone.')
            ->salutation('Kind regards, The Secure Licence Team');
    }
}

<?php

namespace App\Notifications;

use App\Models\InstructorApplication;
use App\Models\InstructorInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorApplicationApproved extends Notification
{
    use Queueable;

    public function __construct(public InstructorApplication $application, public InstructorInvite $invite) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $a = $this->application;
        $setupUrl = route('instructor.invite.show', ['token' => $this->invite->token]);
        return (new MailMessage)
            ->subject("Congratulations! Your Secure Licence application is approved 🎉")
            ->greeting("Hi {$a->first_name},")
            ->line("Great news — your instructor application has been **approved**.")
            ->line("**Reference:** {$a->reference}")
            ->line('Click the button below to set your password and finalise your profile. The link is valid for 7 days.')
            ->action('Set up my account', $setupUrl)
            ->line('Once your profile is live, learners in your area can start booking with you straight away.')
            ->line('Questions? Reply to this email or contact us at support@securelicence.com.')
            ->salutation('Welcome aboard — The Secure Licence team');
    }
}

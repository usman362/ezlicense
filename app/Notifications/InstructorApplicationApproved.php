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
            ->subject("Your Secure Licence application is approved — next step: submit your documents")
            ->greeting("Hi {$a->first_name},")
            ->line("Good news — your instructor application has been **approved** to move to the next stage.")
            ->line("**Reference:** {$a->reference}")
            ->line('**Next step — submit your documents.** Click the button below to set your password and **upload your verification documents** (driver licence, instructor licence and WWCC). The link is valid for 7 days.')
            ->action('Set password & submit documents', $setupUrl)
            ->line('Once our team has **reviewed and approved your documents**, your profile goes live and learners in your area can start booking with you. Until then your account stays pending.')
            ->line('Questions? Reply to this email or contact us at support@securelicence.com.')
            ->salutation('Welcome aboard — The Secure Licence team');
    }
}

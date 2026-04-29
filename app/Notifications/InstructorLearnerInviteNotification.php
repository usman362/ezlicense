<?php

namespace App\Notifications;

use App\Models\InstructorLearnerInvite;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a learner email when an instructor sends them an invite to book lessons.
 */
class InstructorLearnerInviteNotification extends Notification
{
    use Queueable;

    public function __construct(
        public InstructorLearnerInvite $invite,
        public User $instructor,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $this->invite->invitee_name ? trim($this->invite->invitee_name) : 'there';
        $acceptUrl = $this->invite->acceptUrl();

        $msg = (new MailMessage)
            ->subject($this->instructor->name . ' invited you to book driving lessons on Secure Licences')
            ->greeting('Hi ' . $name . ',')
            ->line('**' . $this->instructor->name . '** has invited you to book driving lessons through **Secure Licences** — Australia\'s #1 platform for verified driving instructors.');

        if ($this->invite->personal_message) {
            $msg->line('---')
                ->line('💬 *' . trim($this->invite->personal_message) . '* — ' . $this->instructor->name)
                ->line('---');
        }

        return $msg
            ->line('**Click below to accept the invite and book your first lesson:**')
            ->action('Accept Invite & Book', $acceptUrl)
            ->line('**What happens next?**')
            ->line('• Click the link above to view ' . $this->instructor->name . '\'s profile')
            ->line('• Choose a date and time that suits you')
            ->line('• Pay securely online — no cash needed')
            ->line('• Get reminders and track your lessons in your dashboard')
            ->line('This invite expires in 30 days. If you have any questions, just reply to this email.')
            ->salutation('Drive safe — The Secure Licences Team');
    }
}

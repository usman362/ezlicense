<?php

namespace App\Notifications;

use App\Models\ReferralInvite;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralInviteNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ReferralInvite $invite,
        public User $referrer,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $signupUrl = url('/register?ref=' . $this->referrer->referral_code);
        $name = $this->invite->invitee_name ? trim($this->invite->invitee_name) : 'there';

        $msg = (new MailMessage)
            ->subject($this->referrer->name . ' invited you to Secure Licences 🚗')
            ->greeting('Hi ' . $name . ',')
            ->line('**' . $this->referrer->name . '** thinks you\'d love **Secure Licences** — Australia\'s #1 platform for finding verified driving instructors.');

        if ($this->invite->personal_message) {
            $msg->line('---')
                ->line('💬 *' . trim($this->invite->personal_message) . '* — ' . $this->referrer->name)
                ->line('---');
        }

        return $msg
            ->line('**With Secure Licences you can:**')
            ->line('• Compare 1000+ verified driving instructors')
            ->line('• Read real reviews from learners')
            ->line('• Book online in under 60 seconds')
            ->line('• Pay securely — no cash on the road')
            ->action('Sign Up Now', $signupUrl)
            ->line('It only takes a minute and you\'ll get matched with the best instructors in your area.')
            ->salutation('Drive safe — The Secure Licences Team');
    }
}

<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user, public ?string $tempPassword = null)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $isLearner = $this->user->role === 'learner';
        $isInstructor = $this->user->role === 'instructor';

        $msg = (new MailMessage)
            ->subject('Welcome to Secure Licences, ' . $this->user->name . '!')
            ->greeting('Hi ' . $this->user->name . ',')
            ->line('Welcome to **Secure Licences** — Australia\'s #1 platform for finding verified driving instructors.');

        if ($isLearner) {
            $msg->line('You can now search for driving instructors, book lessons, and manage everything from your learner dashboard.')
                ->action('Go to Your Dashboard', url('/learner/dashboard'))
                ->line('**What you can do:**')
                ->line('• Browse 1000+ verified driving instructors')
                ->line('• Book lessons online in real-time')
                ->line('• Track your progress and manage bookings');
        } elseif ($isInstructor) {
            $msg->line('Your instructor profile is being reviewed by our team. Once approved, you\'ll be able to receive bookings from learners.')
                ->action('Complete Your Profile', url('/instructor/dashboard'))
                ->line('**Next steps:**')
                ->line('• Upload your instructor license and vehicle documents')
                ->line('• Set your service areas and availability')
                ->line('• Add your banking details for payouts');
        } else {
            $msg->action('Sign In', url('/login'));
        }

        if ($this->tempPassword) {
            $msg->line('')
                ->line('**Your temporary password:** `' . $this->tempPassword . '`')
                ->line('Please change this password after your first login for security.');
        }

        return $msg
            ->line('If you have any questions, our support team is here to help.')
            ->salutation('The Secure Licences Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'welcome',
            'title' => 'Welcome to Secure Licences!',
            'body' => 'Your account has been created. Explore the platform.',
            'user_id' => $this->user->id,
        ];
    }
}

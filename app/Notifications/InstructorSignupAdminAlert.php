<?php

namespace App\Notifications;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to all admins when a new instructor signs up so they can verify quickly.
 */
class InstructorSignupAdminAlert extends Notification
{
    use Queueable;

    public function __construct(
        public User $instructor,
        public ?InstructorProfile $profile = null,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $msg = (new MailMessage)
            ->subject('[Action needed] New instructor signup — ' . $this->instructor->name)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('A new instructor just signed up and is awaiting verification.')
            ->line('**Name:** ' . $this->instructor->name)
            ->line('**Email:** ' . $this->instructor->email)
            ->when($this->instructor->phone, fn ($m) => $m->line('**Phone:** ' . $this->instructor->phone))
            ->when($this->profile?->business_name, fn ($m) => $m->line('**Business:** ' . $this->profile->business_name))
            ->when($this->profile?->abn, fn ($m) => $m->line('**ABN:** ' . $this->profile->abn));

        if ($this->profile) {
            $msg->action('Review Instructor', url('/admin/instructors/' . $this->profile->id));
        } else {
            $msg->action('Review in Admin', url('/admin/users/' . $this->instructor->id));
        }

        return $msg
            ->line('Please verify their license, vehicle, and WWCC documents before activating their profile.')
            ->salutation('— Secure Licences System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'instructor_signup',
            'title' => 'New instructor signup',
            'body' => $this->instructor->name . ' just signed up. Verification needed.',
            'instructor_id' => $this->instructor->id,
            'instructor_profile_id' => $this->profile?->id,
        ];
    }
}

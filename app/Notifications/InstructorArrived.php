<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use App\Traits\SendsSms;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class InstructorArrived extends Notification
{
    use Queueable, SendsSms;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return array_merge(['database', 'mail'], $this->smsChannel($notifiable));
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $b = $this->booking;
        $instructorName = $b->instructor->name ?? 'Your instructor';

        return (new VonageMessage)
            ->content("SecureLicences: {$instructorName} has arrived for your lesson (Booking #{$b->id}). Please meet them at the agreed location.");
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'today';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : '';
        $type = $b->type === 'test_package' ? 'Test Package' : 'Driving Lesson';
        $instructorName = $b->instructor->name ?? 'Your instructor';

        return (new MailMessage)
            ->subject("Your Instructor Has Arrived — {$type}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("**{$instructorName}** has arrived for your **{$type}** scheduled on **{$date}** at **{$time}**.")
            ->line('Please meet your instructor at the agreed pickup location.')
            ->action('View Booking Details', url('/learner/dashboard'))
            ->salutation("See you on the road!\nThe {$siteName} Team");
    }

    public function toArray(object $notifiable): array
    {
        $b = $this->booking;
        $type = $b->type === 'test_package' ? 'test package' : 'lesson';
        $instructorName = $b->instructor->name ?? 'Your instructor';

        return [
            'booking_id' => $b->id,
            'type' => 'instructor_arrived',
            'message' => "{$instructorName} has arrived for your {$type} (Booking #{$b->id}).",
        ];
    }
}

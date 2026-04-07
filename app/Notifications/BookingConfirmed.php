<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\SiteSetting;
use App\Services\IcsGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Only add mail if SMTP is configured
        $smtpHost = SiteSetting::get('smtp_host');
        if (! empty($smtpHost)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $b = $this->booking;
        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'TBC';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : 'TBC';
        $type = $b->type === 'test_package' ? 'Test Package' : 'Driving Lesson';

        $message = (new MailMessage)
            ->subject("Booking Confirmed — {$type} on {$date}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("Your booking has been confirmed.")
            ->line("**{$type}** on **{$date}** at **{$time}**")
            ->line("Duration: {$b->duration_minutes} minutes")
            ->action('View My Bookings', url('/learner/dashboard'))
            ->line("Thank you for choosing {$siteName}!");

        // Attach ICS if the instructor has enabled it
        if ($b->instructor_id) {
            $instructorProfile = InstructorProfile::where('user_id', $b->instructor_id)->first();
            if ($instructorProfile && $instructorProfile->attach_ics_to_emails) {
                $ics = IcsGenerator::forBooking($b);
                $message->attachData($ics, 'lesson.ics', ['mime' => 'text/calendar']);
            }
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => 'booking_confirmed',
            'message' => 'Your booking #' . $this->booking->id . ' has been confirmed.',
            'scheduled_at' => $this->booking->scheduled_at?->toIso8601String(),
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequested extends Notification
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        $smtpHost = SiteSetting::get('smtp_host');
        if (! empty($smtpHost)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $b = $this->booking;
        $b->loadMissing(['instructor', 'suburb.state']);

        $siteName = SiteSetting::get('site_name', 'SecureLicences');
        $instructorName = $b->instructor->name ?? 'your instructor';

        // Format lesson details
        $lessonDate = $b->scheduled_at
            ? $b->scheduled_at->format('D, d M Y')
            : 'TBC';

        $startTime = $b->scheduled_at
            ? $b->scheduled_at->format('g:i a')
            : 'TBC';
        $endTime = $b->scheduled_at
            ? $b->scheduled_at->copy()->addMinutes($b->duration_minutes ?? 60)->format('g:i a')
            : '';
        $lessonTime = $endTime ? "{$startTime} - {$endTime}" : $startTime;

        $durationHours = ($b->duration_minutes ?? 60) / 60;
        $lessonType = ($b->type === 'test_package')
            ? '2.5 Hour Test Package'
            : "{$durationHours} Hour Driving Lesson";

        // Location
        $lessonLocation = null;
        if ($b->suburb) {
            $parts = array_filter([
                $b->suburb->name,
                $b->suburb->postcode,
                $b->suburb->state?->code ?? $b->suburb->state?->name ?? null,
            ]);
            $lessonLocation = implode(' ', $parts);
        }

        // URLs
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $reviewUrl = $baseUrl . '/learner/dashboard';
        $newBookingUrl = $baseUrl . '/learner/bookings/new';
        $findInstructorUrl = $baseUrl . '/find-instructor';
        $supportUrl = $baseUrl . '/contact';
        $websiteUrl = $baseUrl;
        $facebookUrl = SiteSetting::get('facebook_url', 'https://www.facebook.com/SecureLicences');
        $instagramUrl = SiteSetting::get('instagram_url', 'https://www.instagram.com/securelicences');

        return (new MailMessage)
            ->subject('How was your booking?')
            ->view('emails.event-complete', [
                'learnerName' => $notifiable->first_name ?? $notifiable->name ?? 'there',
                'instructorName' => $instructorName,
                'lessonDate' => $lessonDate,
                'lessonTime' => $lessonTime,
                'lessonType' => $lessonType,
                'lessonLocation' => $lessonLocation,
                'reviewUrl' => $reviewUrl,
                'newBookingUrl' => $newBookingUrl,
                'findInstructorUrl' => $findInstructorUrl,
                'supportUrl' => $supportUrl,
                'websiteUrl' => $websiteUrl,
                'facebookUrl' => $facebookUrl,
                'instagramUrl' => $instagramUrl,
                'siteName' => $siteName,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $instructorName = $this->booking->instructor->name ?? 'your instructor';

        return [
            'booking_id' => $this->booking->id,
            'instructor_id' => $this->booking->instructor_id,
            'type' => 'review_requested',
            'message' => "How was your learning experience with {$instructorName}? Please leave a rating.",
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to admin/platform email whenever a booking event occurs
 * (new booking, cancellation, completion).
 */
class AdminBookingAlert extends Notification
{
    use Queueable;

    public const EVENT_NEW = 'new_booking';
    public const EVENT_CANCELLED = 'booking_cancelled';
    public const EVENT_COMPLETED = 'booking_completed';
    public const EVENT_PROPOSED = 'booking_proposed';

    public function __construct(
        protected Booking $booking,
        protected string $event = self::EVENT_NEW,
        protected ?string $extraInfo = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $b = $this->booking;
        $b->loadMissing(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state']);

        $date = $b->scheduled_at ? $b->scheduled_at->format('l, d M Y') : 'TBC';
        $time = $b->scheduled_at ? $b->scheduled_at->format('g:i A') : 'TBC';
        $type = $b->type === 'test_package' ? 'Test Package' : 'Driving Lesson';
        $learnerName = $b->learner->name ?? 'Unknown';
        $instructorName = $b->instructor->name ?? 'Unknown';
        $location = $b->suburb ? "{$b->suburb->name}, {$b->suburb->postcode}" : 'Not specified';
        $amount = number_format((float) $b->amount, 2);

        $subject = match ($this->event) {
            self::EVENT_NEW => "[New Booking] {$learnerName} → {$instructorName} on {$date}",
            self::EVENT_CANCELLED => "[Cancelled] Booking #{$b->id} — {$learnerName} / {$instructorName}",
            self::EVENT_COMPLETED => "[Completed] Booking #{$b->id} — {$learnerName} / {$instructorName}",
            self::EVENT_PROPOSED => "[Proposal] {$instructorName} proposed booking to {$learnerName}",
            default => "[Booking Update] #{$b->id}",
        };

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($this->getGreeting());

        // Event-specific intro
        match ($this->event) {
            self::EVENT_NEW => $message->line("A new booking has been placed on {$siteName}."),
            self::EVENT_CANCELLED => $message->line("A booking has been cancelled on {$siteName}."),
            self::EVENT_COMPLETED => $message->line("A booking has been marked as completed on {$siteName}."),
            self::EVENT_PROPOSED => $message->line("An instructor has proposed a new booking on {$siteName}."),
        };

        $learnerEmail = $b->learner->email ?? '—';
        $instructorEmail = $b->instructor->email ?? '—';

        $message
            ->line("**Booking #{$b->id}**")
            ->line("**Type:** {$type}")
            ->line("**Learner:** {$learnerName} ({$learnerEmail})")
            ->line("**Instructor:** {$instructorName} ({$instructorEmail})")
            ->line("**Date:** {$date} at {$time}")
            ->line("**Location:** {$location}")
            ->line("**Amount:** \${$amount} AUD");

        if ($this->event === self::EVENT_CANCELLED && $this->extraInfo) {
            $message->line("**Cancellation Reason:** {$this->extraInfo}");
        }

        $message->action('View in Admin Panel', url('/admin/bookings'));

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'event' => $this->event,
            'type' => 'admin_booking_alert',
            'message' => $this->getShortMessage(),
        ];
    }

    private function getGreeting(): string
    {
        return match ($this->event) {
            self::EVENT_NEW => '🎉 New Booking!',
            self::EVENT_CANCELLED => '❌ Booking Cancelled',
            self::EVENT_COMPLETED => '✅ Booking Completed',
            self::EVENT_PROPOSED => '📋 New Proposal',
            default => 'Booking Update',
        };
    }

    private function getShortMessage(): string
    {
        $this->booking->loadMissing(['learner:id,name', 'instructor:id,name']);
        $learner = $this->booking->learner->name ?? 'Unknown';
        $instructor = $this->booking->instructor->name ?? 'Unknown';

        return match ($this->event) {
            self::EVENT_NEW => "New booking: {$learner} booked with {$instructor}",
            self::EVENT_CANCELLED => "Booking #{$this->booking->id} cancelled ({$learner} / {$instructor})",
            self::EVENT_COMPLETED => "Booking #{$this->booking->id} completed ({$learner} / {$instructor})",
            self::EVENT_PROPOSED => "{$instructor} proposed booking to {$learner}",
            default => "Booking #{$this->booking->id} updated",
        };
    }
}

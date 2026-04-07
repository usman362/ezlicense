<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApproved extends Notification
{
    use Queueable;

    public function __construct(
        protected Review $review
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
        $siteName = SiteSetting::get('site_name', 'Secure Licences');
        $learnerName = $this->review->learner->name ?? 'A learner';

        return (new MailMessage)
            ->subject("New {$this->review->rating}-star review from {$learnerName}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("{$learnerName} left you a **{$this->review->rating}-star** review.")
            ->when($this->review->comment, function ($message) {
                $message->line("\"{$this->review->comment}\"");
            })
            ->action('View Your Profile', url('/instructor/dashboard'))
            ->line("Keep up the great work with {$siteName}!");
    }

    public function toArray(object $notifiable): array
    {
        $learnerName = $this->review->learner->name ?? 'A learner';

        return [
            'review_id' => $this->review->id,
            'type' => 'review_approved',
            'rating' => $this->review->rating,
            'message' => "{$learnerName} left you a {$this->review->rating}-star review.",
        ];
    }
}

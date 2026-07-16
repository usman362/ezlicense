<?php

namespace App\Notifications;

use App\Models\SocialMediaSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the instructor when the admin approves their submission
 * (it will be posted on Secure Licence's social media soon).
 */
class SocialMediaSubmissionApproved extends Notification
{
    use Queueable;

    public function __construct(public SocialMediaSubmission $submission)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $learner = $this->submission->learner_name ?: 'your learner';

        return (new MailMessage)
            ->subject('Your social media submission has been approved ✅')
            ->greeting('Good news ' . ($notifiable->name ?? '') . '!')
            ->line('Your submission celebrating ' . $learner . '\'s ' . strtolower($this->submission->categoryLabel()) . ' has been approved by our team.')
            ->line('We\'ll be sharing it on Secure Licence\'s social media shortly — we\'ll let you know once it\'s live.')
            ->action('View your submissions', url('/instructor/social-media'))
            ->line('Thanks for helping us celebrate your learners\' wins!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'          => 'social_media_approved',
            'title'         => 'Submission approved ✅',
            'body'          => 'Your social media submission' . ($this->submission->learner_name ? ' for ' . $this->submission->learner_name : '') . ' has been approved.',
            'submission_id' => $this->submission->id,
        ];
    }
}

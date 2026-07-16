<?php

namespace App\Notifications;

use App\Models\SocialMediaSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the instructor confirming their social-media submission was received.
 */
class SocialMediaSubmissionReceived extends Notification
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
            ->subject('We\'ve received your social media submission 🎥')
            ->greeting('Thanks ' . ($notifiable->name ?? '') . '!')
            ->line('We\'ve received your submission celebrating ' . $learner . '\'s ' . strtolower($this->submission->categoryLabel()) . '.')
            ->line('Our team will review it and share it on Secure Licence\'s social media — great free promotion for you.')
            ->action('View your submissions', url('/instructor/social-media'))
            ->line('Thanks for helping us celebrate your learners\' wins!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'          => 'social_media_received',
            'title'         => 'Submission received',
            'body'          => 'We\'ve received your social media submission' . ($this->submission->learner_name ? ' for ' . $this->submission->learner_name : '') . '.',
            'submission_id' => $this->submission->id,
        ];
    }
}

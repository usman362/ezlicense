<?php

namespace App\Notifications;

use App\Models\SocialMediaSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the instructor when the admin marks their submission as posted
 * (it's now live on Secure Licence's social media).
 */
class SocialMediaSubmissionPosted extends Notification
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
            ->subject('You\'re live! Your learner win is on our socials 🎉')
            ->greeting('Great news ' . ($notifiable->name ?? '') . '!')
            ->line('Your submission celebrating ' . $learner . ' has been posted on Secure Licence\'s social media.')
            ->action('View your submissions', url('/instructor/social-media'))
            ->line('Thanks for sharing — keep the learner wins coming!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'          => 'social_media_posted',
            'title'         => 'Your submission is live 🎉',
            'body'          => 'Your social media submission' . ($this->submission->learner_name ? ' for ' . $this->submission->learner_name : '') . ' has been posted.',
            'submission_id' => $this->submission->id,
        ];
    }
}

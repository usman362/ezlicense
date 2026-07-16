<?php

namespace App\Notifications;

use App\Models\SocialMediaSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to admins when an instructor uploads new marketing material
 * (learner test-pass video/photos) for the socials.
 */
class NewSocialMediaSubmission extends Notification
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
        $instructor = $this->submission->instructor?->name ?? 'An instructor';
        $learner = $this->submission->learner_name ?: 'a learner';

        return (new MailMessage)
            ->subject('New social media submission from ' . $instructor)
            ->greeting('New marketing material 🎥')
            ->line($instructor . ' has uploaded a new ' . strtolower($this->submission->categoryLabel()) . ' submission for ' . $learner . '.')
            ->when($this->submission->caption, fn ($m) => $m->line('“' . \Illuminate\Support\Str::limit($this->submission->caption, 160) . '”'))
            ->action('Review & post', url('/admin/social-media/' . $this->submission->id))
            ->line('Preview the video/photos and share them on our socials.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'          => 'social_media_submission',
            'title'         => 'New social media submission',
            'body'          => ($this->submission->instructor?->name ?? 'An instructor')
                                . ' uploaded ' . strtolower($this->submission->categoryLabel())
                                . ' material' . ($this->submission->learner_name ? ' for ' . $this->submission->learner_name : '') . '.',
            'submission_id' => $this->submission->id,
        ];
    }
}

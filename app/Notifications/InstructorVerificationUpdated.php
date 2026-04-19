<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorVerificationUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $adminNotes = null,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $notifiable->name;

        return match ($this->status) {
            'verified' => (new MailMessage)
                ->subject('Your Secure Licences Instructor Account Is Approved!')
                ->greeting("Congratulations, {$name}!")
                ->line('Your instructor account has been **verified and approved** by our team.')
                ->line('You can now start receiving bookings from learners across Australia.')
                ->action('Go to Instructor Dashboard', url('/instructor/dashboard'))
                ->line('**What to do next:**')
                ->line('• Review your profile and service areas')
                ->line('• Confirm your availability schedule')
                ->line('• Ensure your banking details are up to date')
                ->line('Welcome to the Secure Licences instructor network!')
                ->salutation('The Secure Licences Team'),

            'rejected' => (new MailMessage)
                ->subject('Update on Your Secure Licences Instructor Application')
                ->greeting("Hi {$name},")
                ->line('We\'ve reviewed your instructor application and unfortunately we\'re unable to approve it at this time.')
                ->when($this->adminNotes, fn ($m) => $m->line('**Reason:** ' . $this->adminNotes))
                ->line('If you believe this was in error or you have additional information to provide, please contact our support team.')
                ->action('Contact Support', url('/contact'))
                ->salutation('The Secure Licences Team'),

            'documents_submitted' => (new MailMessage)
                ->subject('We\'ve Received Your Documents')
                ->greeting("Hi {$name},")
                ->line('We\'ve received your instructor documents and they are now under review.')
                ->line('Our team will review your submission within 1–3 business days and notify you of the outcome.')
                ->action('View Your Profile', url('/instructor/dashboard'))
                ->salutation('The Secure Licences Team'),

            default => (new MailMessage)
                ->subject('Instructor Verification Status Updated')
                ->greeting("Hi {$name},")
                ->line('Your instructor verification status has been updated to: **' . ucfirst(str_replace('_', ' ', $this->status)) . '**')
                ->when($this->adminNotes, fn ($m) => $m->line('Notes: ' . $this->adminNotes))
                ->action('View Dashboard', url('/instructor/dashboard'))
                ->salutation('The Secure Licences Team'),
        };
    }

    public function toArray($notifiable): array
    {
        $map = [
            'verified' => ['Instructor account approved!', 'Your account has been verified. You can now receive bookings.'],
            'rejected' => ['Instructor application declined', 'Your application was not approved. Please contact support for details.'],
            'documents_submitted' => ['Documents received', 'Your documents are under review.'],
        ];

        $info = $map[$this->status] ?? ['Verification updated', 'Your verification status has changed.'];

        return [
            'type' => 'instructor_verification',
            'title' => $info[0],
            'body' => $info[1],
            'status' => $this->status,
            'admin_notes' => $this->adminNotes,
        ];
    }
}

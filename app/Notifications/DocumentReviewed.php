<?php

namespace App\Notifications;

use App\Models\InstructorDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to instructor when admin approves or rejects one of their documents
 * (driving instructor licence, WWCC, vehicle registration, insurance, etc.)
 */
class DocumentReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public InstructorDocument $document,
        public string $outcome, // 'approved' or 'rejected'
        public ?string $reviewerNotes = null,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $docTypeLabel = $this->prettyDocType($this->document->document_type ?? 'document');
        $isApproved = $this->outcome === 'approved';

        if ($isApproved) {
            return (new MailMessage)
                ->subject('✓ Your ' . $docTypeLabel . ' has been approved')
                ->greeting('Hi ' . $notifiable->name . ',')
                ->line('Good news! Your **' . $docTypeLabel . '** has been verified and approved by our admin team.')
                ->when($this->reviewerNotes, fn ($m) => $m->line('**Note from admin:** ' . $this->reviewerNotes))
                ->action('View Your Documents', url('/instructor/settings/documents'))
                ->line('You\'re one step closer to a fully active instructor profile. Keep up the great work!')
                ->salutation('— The Secure Licences Team');
        }

        return (new MailMessage)
            ->subject('Action needed: Your ' . $docTypeLabel . ' was not approved')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('We\'ve reviewed your **' . $docTypeLabel . '** but it couldn\'t be approved at this time.')
            ->when($this->reviewerNotes, fn ($m) => $m->line('**Reason:** ' . $this->reviewerNotes))
            ->line('Please re-upload a valid version so we can complete your verification.')
            ->action('Re-upload Document', url('/instructor/settings/documents'))
            ->line('If you think this is a mistake or need help, please contact our support team.')
            ->salutation('— The Secure Licences Team');
    }

    public function toArray($notifiable): array
    {
        $docTypeLabel = $this->prettyDocType($this->document->document_type ?? 'document');
        $isApproved = $this->outcome === 'approved';

        return [
            'type' => 'document_' . $this->outcome,
            'title' => $isApproved ? $docTypeLabel . ' approved' : $docTypeLabel . ' needs reupload',
            'body' => $isApproved
                ? 'Your ' . $docTypeLabel . ' was verified successfully.'
                : 'Your ' . $docTypeLabel . ' was not approved. ' . ($this->reviewerNotes ?: 'Please re-upload.'),
            'document_id' => $this->document->id,
            'document_type' => $this->document->document_type,
            'outcome' => $this->outcome,
        ];
    }

    private function prettyDocType(string $type): string
    {
        return match (strtolower($type)) {
            'license', 'licence', 'instructor_license' => 'Driving Instructor Licence',
            'wwcc' => 'Working with Children Check (WWCC)',
            'vehicle_rego', 'vehicle_registration' => 'Vehicle Registration',
            'insurance' => 'Insurance Certificate',
            'first_aid' => 'First Aid Certificate',
            'identity', 'id' => 'Identity Document',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }
}

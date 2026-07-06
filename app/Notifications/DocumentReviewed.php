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
        $docTypeLabel = $this->prettyDocType($this->document->type ?? 'document');
        $isApproved = $this->outcome === 'approved';

        if ($isApproved) {
            return (new MailMessage)
                ->subject('Your ' . $docTypeLabel . ' has been verified')
                ->greeting('Hi ' . $notifiable->name . ',')
                ->line('Good news — your **' . $docTypeLabel . '** has been reviewed and verified by our team.')
                ->when($this->reviewerNotes, fn ($m) => $m->line('**Note from our team:** ' . $this->reviewerNotes))
                ->action('View your documents', url('/instructor/settings/documents'))
                ->line('That\'s one more step towards a fully active instructor profile. Once all of your documents are verified, your profile will go live automatically.')
                ->salutation('Kind regards, The Secure Licence Team');
        }

        return (new MailMessage)
            ->subject('Action needed: your ' . $docTypeLabel . ' wasn\'t approved')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('We\'ve reviewed your **' . $docTypeLabel . '**, but unfortunately we weren\'t able to approve it at this stage.')
            ->when($this->reviewerNotes, fn ($m) => $m->line('**Reason:** ' . $this->reviewerNotes))
            ->line('Please re-upload a valid, clear copy so we can finish verifying your account.')
            ->action('Re-upload document', url('/instructor/settings/documents'))
            ->line('If you think this is a mistake or you need a hand, please contact our support team.')
            ->salutation('Kind regards, The Secure Licence Team');
    }

    public function toArray($notifiable): array
    {
        $docTypeLabel = $this->prettyDocType($this->document->type ?? 'document');
        $isApproved = $this->outcome === 'approved';

        return [
            'type' => 'document_' . $this->outcome,
            'title' => $isApproved ? $docTypeLabel . ' approved' : $docTypeLabel . ' needs reupload',
            'body' => $isApproved
                ? 'Your ' . $docTypeLabel . ' was verified successfully.'
                : 'Your ' . $docTypeLabel . ' was not approved. ' . ($this->reviewerNotes ?: 'Please re-upload.'),
            'document_id' => $this->document->id,
            'document_type' => $this->document->type,
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

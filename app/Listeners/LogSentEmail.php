<?php

namespace App\Listeners;

use App\Models\EmailLog;
use App\Models\MailboxMessage;
use App\Models\User;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

/**
 * Listens to Laravel Mail events and persists every email send to the
 * `email_logs` table (audit) and the `mailbox_messages` table (webmail "Sent").
 */
class LogSentEmail
{
    public function handleSent(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $to = collect($message->getTo())->first();
            if (! $to) return;

            $headers = $message->getHeaders();
            $notificationClass = $headers->has('X-Notification-Type')
                ? $headers->get('X-Notification-Type')->getBodyAsString()
                : null;

            $user = User::where('email', $to->getAddress())->first();

            EmailLog::create([
                'to_address' => $to->getAddress(),
                'to_name' => $to->getName() ?: null,
                'subject' => $message->getSubject() ?: '(no subject)',
                'notification_class' => $notificationClass,
                'user_id' => $user?->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging failures break email sending
            Log::warning('EmailLog persistence failed: ' . $e->getMessage());
        }

        // Store a full copy in the webmail "Sent" folder.
        $this->storeInMailbox($event);
    }

    /** Persist a full copy of the outgoing email to the webmail mailbox. */
    protected function storeInMailbox(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $to = collect($message->getTo())->first();
            if (! $to) return;

            $from    = collect($message->getFrom())->first();
            $cc      = collect($message->getCc())->map(fn ($a) => $a->getAddress())->implode(', ');
            $replyTo = collect($message->getReplyTo())->map(fn ($a) => $a->getAddress())->implode(', ');

            $html = method_exists($message, 'getHtmlBody') ? $message->getHtmlBody() : null;
            $text = method_exists($message, 'getTextBody') ? $message->getTextBody() : null;
            $html = is_string($html) ? $html : null;
            $text = is_string($text) ? $text : null;

            $headers     = $message->getHeaders();
            $messageId   = $headers->has('Message-ID') ? $headers->get('Message-ID')->getBodyAsString() : null;
            $inReplyTo   = $headers->has('In-Reply-To') ? $headers->get('In-Reply-To')->getBodyAsString() : null;

            MailboxMessage::create([
                'direction'   => MailboxMessage::DIRECTION_OUTBOUND,
                'from_email'  => $from?->getAddress() ?: config('mail.from.address'),
                'from_name'   => $from?->getName() ?: config('mail.from.name'),
                'to_email'    => $to->getAddress(),
                'to_name'     => $to->getName() ?: null,
                'cc'          => $cc ?: null,
                'reply_to'    => $replyTo ?: null,
                'subject'     => $message->getSubject() ?: '(no subject)',
                'body_html'   => $html,
                'body_text'   => $text,
                'preview'     => MailboxMessage::makePreview($html, $text),
                'message_id'  => $messageId,
                'in_reply_to' => $inReplyTo,
                'status'      => 'sent',
                'is_read'     => true,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Mailbox (sent) persistence failed: ' . $e->getMessage());
        }
    }
}

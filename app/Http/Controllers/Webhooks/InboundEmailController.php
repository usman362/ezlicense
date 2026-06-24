<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\MailboxMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives inbound emails from an email-routing provider (Mailgun, Postmark,
 * SendGrid Inbound Parse, Cloudflare Worker, etc.) and stores them in the
 * `mailbox_messages` table so they appear in the admin webmail Inbox.
 *
 * Configure the provider to POST incoming mail to:
 *     POST /webhooks/inbound-email?token=YOUR_SECRET
 *
 * The payload key names differ per provider, so we read the common variants.
 */
class InboundEmailController extends Controller
{
    public function store(Request $request)
    {
        // Shared-secret check (only enforced if a secret is configured).
        $secret = config('services.inbound_email.secret');
        if ($secret && ! hash_equals((string) $secret, (string) $request->query('token', $request->header('X-Webhook-Token')))) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        try {
            $data = $request->all();

            $fromRaw = $this->first($data, ['from', 'sender', 'From', 'from_email']);
            $toRaw   = $this->first($data, ['recipient', 'to', 'To', 'to_email']);
            $subject = $this->first($data, ['subject', 'Subject']) ?: '(no subject)';
            $html    = $this->first($data, ['html', 'body-html', 'HtmlBody', 'stripped-html', 'body_html']);
            $text    = $this->first($data, ['text', 'body-plain', 'TextBody', 'stripped-text', 'body_text', 'plain']);
            $msgId   = $this->first($data, ['Message-Id', 'message-id', 'MessageID', 'message_id']);
            $inReply = $this->first($data, ['In-Reply-To', 'in-reply-to', 'in_reply_to']);
            $cc      = $this->first($data, ['cc', 'Cc']);

            [$fromName, $fromEmail] = $this->parseAddress($fromRaw);
            [, $toEmail] = $this->parseAddress($toRaw);

            // Attachment metadata (we store names/counts, not the binaries here).
            $attachments = $this->first($data, ['attachments', 'Attachments']);
            $hasAttach = ! empty($attachments) || (int) $this->first($data, ['attachment-count', 'attachments']) > 0;

            MailboxMessage::create([
                'direction'       => MailboxMessage::DIRECTION_INBOUND,
                'from_email'      => $fromEmail,
                'from_name'       => $fromName,
                'to_email'        => $toEmail,
                'cc'              => is_string($cc) ? $cc : null,
                'subject'         => $subject,
                'body_html'       => is_string($html) ? $html : null,
                'body_text'       => is_string($text) ? $text : null,
                'preview'         => MailboxMessage::makePreview(is_string($html) ? $html : null, is_string($text) ? $text : null),
                'message_id'      => is_string($msgId) ? $msgId : null,
                'in_reply_to'     => is_string($inReply) ? $inReply : null,
                'status'          => 'received',
                'is_read'         => false,
                'has_attachments' => $hasAttach,
                'attachments'     => is_array($attachments) ? $attachments : null,
                'meta'            => ['ip' => $request->ip()],
            ]);

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Inbound email webhook failed: ' . $e->getMessage());

            // Return 200 anyway so the provider does not endlessly retry on a parse error.
            return response()->json(['ok' => false], 200);
        }
    }

    /** Return the first present, non-empty value among the given keys. */
    private function first(array $data, array $keys): mixed
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $data) && $data[$k] !== '' && $data[$k] !== null) {
                return $data[$k];
            }
        }

        return null;
    }

    /** Split "Name <email@x.com>" into [name, email]. */
    private function parseAddress(?string $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [null, null];
        }
        if (preg_match('/^(.*)<([^>]+)>\s*$/', $raw, $m)) {
            return [trim($m[1], " \"'") ?: null, trim($m[2])];
        }

        return [null, $raw];
    }
}

<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\MailboxMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        // Shared-secret check. If a secret is configured it is enforced; if it is NOT
        // configured we accept but log loudly, because an unsecured endpoint lets anyone
        // inject messages into the admin inbox. Set INBOUND_EMAIL_SECRET to close this.
        $secret = config('services.inbound_email.secret');
        if (empty($secret)) {
            Log::warning('Inbound email webhook accepted WITHOUT a shared secret — set INBOUND_EMAIL_SECRET (services.inbound_email.secret) to secure this endpoint.');
        } elseif (! hash_equals((string) $secret, (string) $request->query('token', $request->header('X-Webhook-Token')))) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        try {
            $payload = $request->all();

            // Resend (and most webhook providers) wrap the email under a "data" key
            // and may include an event "type". Unwrap it; fall back to the flat payload.
            $eventType = $this->first($payload, ['type']);

            // Only inbound/received emails belong in the Inbox. If "All Events" is
            // selected on the provider, ignore delivery-status events
            // (email.sent / delivered / bounced / opened / clicked / delayed / complained).
            if ($eventType && ! \Illuminate\Support\Str::contains(strtolower($eventType), ['received', 'inbound'])) {
                return response()->json(['ok' => true, 'ignored' => $eventType]);
            }

            $data = (isset($payload['data']) && is_array($payload['data'])) ? $payload['data'] : $payload;

            $fromRaw = $this->first($data, ['from', 'sender', 'From', 'from_email']);
            $toRaw   = $this->first($data, ['to', 'recipient', 'To', 'to_email']);
            $subject = $this->first($data, ['subject', 'Subject']) ?: '(no subject)';
            $html    = $this->first($data, ['html', 'body-html', 'HtmlBody', 'stripped-html', 'body_html']);
            $text    = $this->first($data, ['text', 'body-plain', 'TextBody', 'stripped-text', 'body_text', 'plain']);
            $msgId   = $this->first($data, ['message_id', 'Message-Id', 'message-id', 'MessageID']);
            $inReply = $this->first($data, ['in_reply_to', 'In-Reply-To', 'in-reply-to']);
            $ccRaw   = $this->first($data, ['cc', 'Cc']);

            [$fromName, $fromEmail] = $this->normalizeAddress($fromRaw);
            [, $toEmail]            = $this->normalizeAddress($toRaw);
            $cc = $this->addressListToString($ccRaw);

            // Attachment metadata (we store names/counts, not the binaries here).
            $attachments = $this->first($data, ['attachments', 'Attachments']);
            $hasAttach = is_array($attachments)
                ? count($attachments) > 0
                : ((int) $this->first($data, ['attachment-count']) > 0);

            $message = MailboxMessage::create([
                'direction'       => MailboxMessage::DIRECTION_INBOUND,
                'from_email'      => $fromEmail,
                'from_name'       => $fromName,
                'to_email'        => $toEmail,
                'cc'              => $cc ?: null,
                'subject'         => is_string($subject) ? $subject : '(no subject)',
                'body_html'       => is_string($html) ? $html : null,
                'body_text'       => is_string($text) ? $text : null,
                'preview'         => MailboxMessage::makePreview(is_string($html) ? $html : null, is_string($text) ? $text : null),
                'message_id'      => is_string($msgId) ? $msgId : null,
                'in_reply_to'     => is_string($inReply) ? $inReply : null,
                'status'          => 'received',
                'is_read'         => false,
                'has_attachments' => $hasAttach,
                'attachments'     => is_array($attachments) ? $attachments : null,
                'meta'            => ['ip' => $request->ip(), 'event' => $eventType, 'raw' => $payload],
            ]);

            // Resend's inbound webhook carries only metadata — the body must be
            // fetched from the Resend API using the email_id it provides.
            if (! $message->body_html && ! $message->body_text) {
                $emailId = $this->first($data, ['email_id', 'id']);
                if ($emailId) {
                    $this->fetchBodyFromResend($message, (string) $emailId);
                }
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Inbound email webhook failed: ' . $e->getMessage());

            // Return 200 anyway so the provider does not endlessly retry on a parse error.
            return response()->json(['ok' => false], 200);
        }
    }

    /**
     * Fetch the full email body (html/text) from the Resend API and fill it in.
     * Resend stores the email; the inbound webhook only sends metadata + email_id.
     */
    private function fetchBodyFromResend(MailboxMessage $message, string $emailId): void
    {
        $apiKey = config('services.resend.key');
        if (! $apiKey) {
            return;
        }

        try {
            // Received (inbound) emails use a dedicated endpoint — NOT /emails/{id}
            // (that one is for sent emails). html_format=data_uri inlines images so
            // they render in the webmail iframe without serving cid attachments.
            $resp = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(15)
                ->retry(2, 1500)   // body may be a beat behind the webhook; retry briefly
                ->get("https://api.resend.com/emails/receiving/{$emailId}", ['html_format' => 'data_uri']);

            if (! $resp->successful()) {
                Log::warning("Resend retrieve inbound email {$emailId} failed: {$resp->status()} {$resp->body()}");

                return;
            }

            $body = $resp->json();
            $html = $body['html'] ?? null;
            $text = $body['text'] ?? null;

            if (! is_string($html) && ! is_string($text)) {
                return;
            }

            $message->update([
                'body_html' => is_string($html) ? $html : $message->body_html,
                'body_text' => is_string($text) ? $text : $message->body_text,
                'preview'   => MailboxMessage::makePreview(
                    is_string($html) ? $html : null,
                    is_string($text) ? $text : null
                ) ?: $message->preview,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Resend retrieve inbound email {$emailId} error: " . $e->getMessage());
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

    /**
     * Normalize an address that may be: a string ("Name <e@x.com>" or "e@x.com"),
     * an object (['name' => ..., 'email' => ...] / ['address' => ...]),
     * or a list of either (take the first). Returns [name, email].
     */
    private function normalizeAddress(mixed $value): array
    {
        // List of addresses → take the first.
        if (is_array($value) && array_is_list($value) && isset($value[0])) {
            return $this->normalizeAddress($value[0]);
        }

        // Object form: {name, email} or {address}.
        if (is_array($value)) {
            $email = $value['email'] ?? $value['address'] ?? null;
            $name  = $value['name'] ?? null;
            if ($email) {
                return [$name ?: null, $email];
            }
            // Fallback: first scalar value in the array.
            $value = collect($value)->first(fn ($v) => is_string($v));
        }

        return $this->parseAddress(is_string($value) ? $value : null);
    }

    /** Flatten an address or list of addresses into a comma-separated string of emails. */
    private function addressListToString(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value)) {
            $emails = [];
            foreach (array_is_list($value) ? $value : [$value] as $item) {
                [, $email] = $this->normalizeAddress($item);
                if ($email) {
                    $emails[] = $email;
                }
            }

            return $emails ? implode(', ', $emails) : null;
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

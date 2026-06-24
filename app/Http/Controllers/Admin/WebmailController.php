<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailboxMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebmailController extends Controller
{
    /** Inbox — received emails. */
    public function inbox(Request $request)
    {
        $messages = MailboxMessage::inbound()
            ->when($request->query('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('subject', 'like', "%{$s}%")
                  ->orWhere('from_email', 'like', "%{$s}%")
                  ->orWhere('preview', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.webmail.list', [
            'messages' => $messages,
            'folder'   => 'inbox',
            'unread'   => MailboxMessage::inbound()->unread()->count(),
        ]);
    }

    /** Sent — outbound emails (composed + system). */
    public function sent(Request $request)
    {
        $messages = MailboxMessage::outbound()
            ->when($request->query('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('subject', 'like', "%{$s}%")
                  ->orWhere('to_email', 'like', "%{$s}%")
                  ->orWhere('preview', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.webmail.list', [
            'messages' => $messages,
            'folder'   => 'sent',
            'unread'   => MailboxMessage::inbound()->unread()->count(),
        ]);
    }

    /** Read a single message. */
    public function show(MailboxMessage $message)
    {
        if ($message->direction === MailboxMessage::DIRECTION_INBOUND && ! $message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }

        return view('admin.pages.webmail.show', [
            'message' => $message,
            'unread'  => MailboxMessage::inbound()->unread()->count(),
        ]);
    }

    /** Compose form (optionally pre-filled as a reply to $message). */
    public function compose(Request $request)
    {
        $reply = null;
        if ($id = $request->query('reply')) {
            $reply = MailboxMessage::find($id);
        }

        $prefill = [
            'to'      => $reply?->from_email,
            'subject' => $reply ? 'Re: ' . preg_replace('/^(Re:\s*)+/i', '', (string) $reply->subject) : '',
            'reply_to_id' => $reply?->id,
        ];

        return view('admin.pages.webmail.compose', [
            'prefill' => $prefill,
            'reply'   => $reply,
            'from'    => config('services.webmail.address'),
            'unread'  => MailboxMessage::inbound()->unread()->count(),
        ]);
    }

    /** Send a composed email via Resend; the MessageSent listener stores the Sent copy. */
    public function send(Request $request)
    {
        $data = $request->validate([
            'to'          => ['required', 'email'],
            'cc'          => ['nullable', 'string'],
            'subject'     => ['required', 'string', 'max:255'],
            'body'        => ['required', 'string'],
            'reply_to_id' => ['nullable', 'integer', 'exists:mailbox_messages,id'],
        ]);

        $fromAddr = config('services.webmail.address');
        $fromName = config('services.webmail.name');

        // Body is rich HTML from the TinyMCE editor (admin-composed, trusted).
        $bodyHtml = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#1a1d21;line-height:1.6;">'
            . $data['body']
            . '</div>';

        // Threading: reference the original inbound Message-ID if this is a reply.
        $inReplyTo = null;
        if (! empty($data['reply_to_id'])) {
            $inReplyTo = optional(MailboxMessage::find($data['reply_to_id']))->message_id;
        }

        $ccList = collect(explode(',', (string) ($data['cc'] ?? '')))
            ->map(fn ($e) => trim($e))->filter()->all();

        try {
            Mail::html($bodyHtml, function ($m) use ($data, $fromAddr, $fromName, $ccList, $inReplyTo) {
                $m->from($fromAddr, $fromName)
                  ->replyTo($fromAddr, $fromName)
                  ->to($data['to'])
                  ->subject($data['subject']);

                if ($ccList) {
                    $m->cc($ccList);
                }
                if ($inReplyTo) {
                    $m->getSymfonyMessage()->getHeaders()->addTextHeader('In-Reply-To', $inReplyTo);
                }
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Send failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.webmail.sent')->with('message', 'Email sent to ' . $data['to'] . '.');
    }

    /** Toggle read/unread (inbox). */
    public function toggleRead(MailboxMessage $message)
    {
        $message->update([
            'is_read' => ! $message->is_read,
            'read_at' => $message->is_read ? null : now(),
        ]);

        return back();
    }

    public function destroy(MailboxMessage $message)
    {
        $folder = $message->direction === MailboxMessage::DIRECTION_INBOUND ? 'inbox' : 'sent';
        $message->delete();

        return redirect()->route("admin.webmail.{$folder}")->with('message', 'Message deleted.');
    }
}

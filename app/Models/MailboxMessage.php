<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MailboxMessage extends Model
{
    public const DIRECTION_INBOUND = 'inbound';
    public const DIRECTION_OUTBOUND = 'outbound';

    protected $fillable = [
        'direction', 'from_email', 'from_name', 'to_email', 'to_name', 'cc', 'reply_to',
        'subject', 'body_html', 'body_text', 'preview', 'message_id', 'in_reply_to',
        'status', 'is_read', 'read_at', 'has_attachments', 'attachments', 'meta', 'error',
    ];

    protected $casts = [
        'is_read'         => 'boolean',
        'read_at'         => 'datetime',
        'has_attachments' => 'boolean',
        'attachments'     => 'array',
        'meta'            => 'array',
    ];

    public function scopeInbound($q)
    {
        return $q->where('direction', self::DIRECTION_INBOUND);
    }

    public function scopeOutbound($q)
    {
        return $q->where('direction', self::DIRECTION_OUTBOUND);
    }

    public function scopeUnread($q)
    {
        return $q->where('is_read', false);
    }

    /** Build a short plain-text preview from html/text body. */
    public static function makePreview(?string $html, ?string $text): string
    {
        $base = trim((string) ($text ?: strip_tags((string) $html)));
        $base = preg_replace('/\s+/', ' ', $base);

        return Str::limit($base, 180);
    }

    /** Display name for the "other party" depending on direction. */
    public function getPartyAttribute(): string
    {
        if ($this->direction === self::DIRECTION_INBOUND) {
            return $this->from_name ?: ($this->from_email ?: 'Unknown sender');
        }

        return $this->to_name ?: ($this->to_email ?: 'Unknown recipient');
    }
}

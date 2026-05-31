<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SupportRequest extends Model
{
    use HasFactory;

    public const STATUS_NEW       = 'new';
    public const STATUS_OPEN      = 'open';
    public const STATUS_PENDING   = 'pending';
    public const STATUS_RESOLVED  = 'resolved';
    public const STATUS_CLOSED    = 'closed';

    protected $fillable = [
        'reference', 'name', 'email', 'phone', 'role', 'topic',
        'subject', 'message', 'attachments', 'status',
        'assigned_to', 'user_id', 'admin_notes', 'response', 'responded_at', 'responded_by',
    ];

    protected function casts(): array
    {
        return [
            'attachments'  => 'array',
            'responded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $r) {
            if (empty($r->reference)) {
                $r->reference = self::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        do {
            $candidate = 'SLR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (self::where('reference', $candidate)->exists());
        return $candidate;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_NEW       => 'primary',
            self::STATUS_OPEN      => 'info',
            self::STATUS_PENDING   => 'warning',
            self::STATUS_RESOLVED  => 'success',
            self::STATUS_CLOSED    => 'secondary',
            default                => 'secondary',
        };
    }
}

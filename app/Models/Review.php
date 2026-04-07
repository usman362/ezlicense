<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    // ── Review moderation statuses ─────────────────────────────
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'booking_id',
        'learner_id',
        'instructor_id',
        'rating',
        'comment',
        'status',
        'rejection_reason',
        'moderated_at',
        'moderated_by',
        'is_hidden',
        'google_review_prompted',
    ];

    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
            'google_review_prompted' => 'boolean',
            'moderated_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    // ── Scopes ─────────────────────────────────────────────────

    /**
     * Only approved, visible reviews (for public display).
     */
    public function scopePublic($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
                     ->where('is_hidden', false);
    }

    /**
     * Pending reviews awaiting admin moderation.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ── Status helpers ─────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}

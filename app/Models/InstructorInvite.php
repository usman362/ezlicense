<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InstructorInvite extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_ACCEPTED  = 'accepted';
    public const STATUS_EXPIRED   = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    public const DEFAULT_EXPIRY_DAYS = 7;

    protected $fillable = [
        'token',
        'email',
        'first_name',
        'last_name',
        'phone',
        'years_experience',
        'transmission',
        'bio',
        'suburb_id',
        'lesson_price',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'personal_note',
        'invited_by_user_id',
        'registered_user_id',
        'status',
        'expires_at',
        'accepted_at',
        'last_sent_at',
        'send_count',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'      => 'datetime',
            'accepted_at'     => 'datetime',
            'last_sent_at'    => 'datetime',
            'send_count'      => 'integer',
            'years_experience'=> 'integer',
            'lesson_price'    => 'decimal:2',
            'vehicle_year'    => 'integer',
        ];
    }

    public function suburb()
    {
        return $this->belongsTo(\App\Models\Suburb::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $invite) {
            if (empty($invite->token)) {
                $invite->token = self::generateToken();
            }
            if (empty($invite->expires_at)) {
                $invite->expires_at = now()->addDays(self::DEFAULT_EXPIRY_DAYS);
            }
            if (empty($invite->last_sent_at)) {
                $invite->last_sent_at = now();
            }
            $invite->email = strtolower(trim($invite->email));
        });
    }

    /* ─────────────── Relations ─────────────── */

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function registeredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }

    /* ─────────────── Scopes ─────────────── */

    public function scopePending($q)   { return $q->where('status', self::STATUS_PENDING); }
    public function scopeAccepted($q)  { return $q->where('status', self::STATUS_ACCEPTED); }
    public function scopeCancelled($q) { return $q->where('status', self::STATUS_CANCELLED); }

    /* ─────────────── Helpers ─────────────── */

    public static function generateToken(): string
    {
        do {
            $token = Str::random(48);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }

    /**
     * Can this invite still be redeemed? Must be PENDING and within expiry window.
     */
    public function isUsable(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->isExpired();
    }

    /**
     * Sync the status field if the row has technically expired but the cron
     * hasn't run yet — so display logic always sees the truth.
     */
    public function syncStatus(): self
    {
        if ($this->status === self::STATUS_PENDING && $this->isExpired()) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
        return $this;
    }

    public function markAccepted(User $user): void
    {
        $this->update([
            'status'             => self::STATUS_ACCEPTED,
            'registered_user_id' => $user->id,
            'accepted_at'        => now(),
        ]);
    }

    /**
     * Public URL the invitee clicks from the email.
     */
    public function url(): string
    {
        return route('instructor.invite.show', ['token' => $this->token]);
    }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: $this->email;
    }
}

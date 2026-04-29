<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InstructorLearnerInvite extends Model
{
    protected $fillable = [
        'instructor_user_id',
        'invitee_email',
        'invitee_name',
        'personal_message',
        'invite_token',
        'email_sent_at',
        'accepted_by_user_id',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $invite) {
            if (empty($invite->invite_token)) {
                $invite->invite_token = self::generateToken();
            }
            if (empty($invite->expires_at)) {
                $invite->expires_at = now()->addDays(30);
            }
        });
    }

    public static function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (self::where('invite_token', $token)->exists());

        return $token;
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_user_id');
    }

    public function acceptedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && ! $this->isAccepted();
    }

    public function acceptUrl(): string
    {
        return url('/instructor-invite/' . $this->invite_token);
    }
}

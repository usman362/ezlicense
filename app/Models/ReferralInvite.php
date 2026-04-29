<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralInvite extends Model
{
    protected $fillable = [
        'referrer_user_id',
        'invitee_email',
        'invitee_name',
        'personal_message',
        'email_sent_at',
        'signed_up_user_id',
        'signed_up_at',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'signed_up_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function signedUpUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_up_user_id');
    }

    public function hasConverted(): bool
    {
        return $this->signed_up_at !== null;
    }
}

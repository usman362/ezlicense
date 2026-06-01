<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedSignupAttempt extends Model
{
    protected $fillable = [
        'blocked_signup_id', 'email', 'phone', 'attempted_name',
        'ip_address', 'user_agent', 'context',
    ];

    public function blockedSignup(): BelongsTo
    {
        return $this->belongsTo(BlockedSignup::class);
    }
}

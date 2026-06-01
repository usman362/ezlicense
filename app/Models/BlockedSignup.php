<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockedSignup extends Model
{
    protected $fillable = [
        'original_user_id', 'email', 'phone_normalized', 'name',
        'reason', 'blocked_by_user_id', 'blocked_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    public function originalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by_user_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(BlockedSignupAttempt::class)->latest();
    }
}

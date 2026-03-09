<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearnerWallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'non_refundable_credit'];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'non_refundable_credit' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LearnerTransaction::class, 'user_id', 'user_id');
    }
}

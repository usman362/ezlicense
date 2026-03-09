<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnerTransaction extends Model
{
    public const TYPE_CREDIT_PURCHASE = 'credit_purchase';
    public const TYPE_LESSON_PAYMENT = 'lesson_payment';
    public const TYPE_REFUND = 'refund';

    protected $fillable = ['user_id', 'type', 'description', 'amount', 'balance_after', 'booking_id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

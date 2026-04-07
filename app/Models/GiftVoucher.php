<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GiftVoucher extends Model
{
    const TYPE_1HOUR = '1hour';
    const TYPE_5HOUR = '5hour';
    const TYPE_CUSTOM = 'custom';

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_ACTIVE = 'active';
    const STATUS_REDEEMED = 'redeemed';
    const STATUS_PARTIALLY_REDEEMED = 'partially_redeemed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    const PRICES = [
        self::TYPE_1HOUR => 65.00,
        self::TYPE_5HOUR => 300.00,
    ];

    protected $fillable = [
        'code', 'purchaser_id', 'redeemer_id',
        'purchaser_name', 'purchaser_email',
        'recipient_name', 'recipient_email', 'personal_message',
        'amount', 'remaining_amount', 'voucher_type',
        'status', 'payment_method', 'payment_reference',
        'paid_at', 'redeemed_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'redeemed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public static function generateCode(): string
    {
        do {
            $code = 'SL-' . strtoupper(Str::random(10));
        } while (static::where('code', $code)->exists());
        return $code;
    }

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchaser_id');
    }

    public function redeemer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemer_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_PARTIALLY_REDEEMED])
            && $this->remaining_amount > 0
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_1HOUR => '1 Hour Lesson',
            self::TYPE_5HOUR => '5 Hour Package',
            self::TYPE_CUSTOM => 'Custom Amount',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Payment',
            self::STATUS_PAID => 'Paid',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_REDEEMED => 'Fully Redeemed',
            self::STATUS_PARTIALLY_REDEEMED => 'Partially Redeemed',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}

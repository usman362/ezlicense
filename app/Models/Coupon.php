<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'type', 'amount',
        'min_order_amount', 'max_discount_amount',
        'max_uses', 'max_uses_per_user', 'used_count',
        'starts_at', 'expires_at', 'is_active', 'first_booking_only',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'first_booking_only' => 'boolean',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /**
     * Lookup an active, redeemable coupon by code.
     * Returns null if not found / inactive / expired / out of uses.
     */
    public static function findActive(string $code): ?self
    {
        $coupon = static::where('code', strtoupper(trim($code)))->first();
        if (! $coupon || ! $coupon->isRedeemable()) {
            return null;
        }
        return $coupon;
    }

    public function isRedeemable(): bool
    {
        if (! $this->is_active) return false;
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->expires_at && $now->gt($this->expires_at)) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    /**
     * Check whether a specific user can still redeem this coupon.
     * Returns ['ok' => bool, 'reason' => string].
     */
    public function checkForUser(\App\Models\User $user, float $orderTotal): array
    {
        if (! $this->isRedeemable()) {
            return ['ok' => false, 'reason' => 'This coupon is no longer available.'];
        }
        if ($this->min_order_amount > 0 && $orderTotal < (float) $this->min_order_amount) {
            return ['ok' => false, 'reason' => 'Minimum order $' . number_format($this->min_order_amount, 2) . ' required.'];
        }
        if ($this->max_uses_per_user > 0) {
            $userCount = $this->redemptions()->where('user_id', $user->id)->count();
            if ($userCount >= $this->max_uses_per_user) {
                return ['ok' => false, 'reason' => 'You have already used this coupon.'];
            }
        }
        if ($this->first_booking_only) {
            $hasPriorBooking = \App\Models\Booking::where('learner_id', $user->id)
                ->whereIn('payment_status', ['paid', 'refunded'])
                ->exists();
            if ($hasPriorBooking) {
                return ['ok' => false, 'reason' => 'This coupon is for first-time bookings only.'];
            }
        }
        return ['ok' => true, 'reason' => ''];
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->type === 'percent') {
            $discount = round($orderTotal * ((float) $this->amount / 100), 2);
            if ($this->max_discount_amount !== null && $discount > (float) $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }
        } else { // fixed
            $discount = min((float) $this->amount, $orderTotal);
        }
        return max(0, round($discount, 2));
    }
}

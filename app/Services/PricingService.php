<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\ReferralInvite;
use App\Models\SiteSetting;
use App\Models\User;

/**
 * Central pricing brain.
 * Reads all rates/tiers/discounts from SiteSetting so admins can adjust without redeploys.
 */
class PricingService
{
    /* ================================================================
     | Bulk-hours discount (e.g. 6 hrs → 5%, 10 hrs → 10%)
     |================================================================ */

    /**
     * Returns the configured tier list as `[['hours' => N, 'discount_pct' => P], ...]`,
     * sorted ascending by hours. Falls back to legacy 5%/10% defaults if missing.
     */
    public function getDiscountTiers(): array
    {
        $tiers = SiteSetting::get('hours_discount_tiers', []);
        if (! is_array($tiers) || empty($tiers)) {
            $tiers = [
                ['hours' => 6, 'discount_pct' => 5],
                ['hours' => 10, 'discount_pct' => 10],
            ];
        }

        $clean = [];
        foreach ($tiers as $row) {
            $h = isset($row['hours']) ? (int) $row['hours'] : 0;
            $p = isset($row['discount_pct']) ? (float) $row['discount_pct'] : 0;
            if ($h > 0 && $p > 0) {
                $clean[] = ['hours' => $h, 'discount_pct' => $p];
            }
        }
        usort($clean, fn ($a, $b) => $a['hours'] <=> $b['hours']);
        return $clean;
    }

    /**
     * Returns the discount percent that applies for a given lesson-hours total.
     * Picks the best (highest) tier the hours qualify for.
     */
    public function discountPctForHours(int $hours): float
    {
        $best = 0;
        foreach ($this->getDiscountTiers() as $tier) {
            if ($hours >= $tier['hours']) {
                $best = max($best, (float) $tier['discount_pct']);
            }
        }
        return $best;
    }

    /* ================================================================
     | Booking hour-package options (radio buttons on Step 2)
     |================================================================ */

    public function getBookingHourPackages(): array
    {
        $opts = SiteSetting::get('booking_hour_packages', []);
        if (! is_array($opts) || empty($opts)) {
            return [1, 3, 5, 10, 20];
        }
        $clean = array_values(array_unique(array_filter(array_map('intval', $opts), fn ($n) => $n > 0)));
        sort($clean);
        return $clean;
    }

    /* ================================================================
     | Platform fees & GST
     |================================================================ */

    public function platformFeePercent(): float
    {
        return (float) SiteSetting::get('platform_fee_percent', 4);
    }

    public function gstRatePercent(): float
    {
        return (float) SiteSetting::get('gst_rate_percent', 10);
    }

    public function defaultTestPackagePrice(): float
    {
        return (float) SiteSetting::get('default_test_package_price', 225);
    }

    public function platformServiceFee(): float
    {
        return (float) SiteSetting::get('platform_service_fee', 5);
    }

    public function paymentProcessingFee(): float
    {
        return (float) SiteSetting::get('payment_processing_fee', 2);
    }

    /* ================================================================
     | Coupons
     |================================================================ */

    /**
     * Validate a coupon code for a user + order total.
     * Returns: ['ok' => bool, 'coupon' => Coupon|null, 'discount' => float, 'reason' => string]
     */
    public function validateCoupon(?string $code, ?User $user, float $orderTotal): array
    {
        if (! $code) {
            return ['ok' => false, 'coupon' => null, 'discount' => 0, 'reason' => 'No code provided.'];
        }
        $coupon = Coupon::findActive($code);
        if (! $coupon) {
            return ['ok' => false, 'coupon' => null, 'discount' => 0, 'reason' => 'Invalid or expired coupon code.'];
        }
        if ($user) {
            $check = $coupon->checkForUser($user, $orderTotal);
            if (! $check['ok']) {
                return ['ok' => false, 'coupon' => $coupon, 'discount' => 0, 'reason' => $check['reason']];
            }
        } else {
            // Guest user — only do basic redeemability checks (no per-user history)
            if ($coupon->min_order_amount > 0 && $orderTotal < (float) $coupon->min_order_amount) {
                return ['ok' => false, 'coupon' => $coupon, 'discount' => 0, 'reason' => 'Minimum order $' . number_format($coupon->min_order_amount, 2) . ' required.'];
            }
        }
        return [
            'ok' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($orderTotal),
            'reason' => '',
        ];
    }

    /* ================================================================
     | Referral discount (for invitee's first booking)
     |================================================================ */

    /**
     * If this learner was referred and is making their first booking,
     * return the discount amount they qualify for.
     */
    public function referralDiscountFor(User $user, float $orderTotal): float
    {
        if (! (bool) SiteSetting::get('referral_enabled', true)) return 0;
        if (! $user->referred_by_user_id) return 0;

        // Already used a referral discount on a prior booking?
        $used = \App\Models\Booking::where('learner_id', $user->id)
            ->where('referral_discount_amount', '>', 0)
            ->exists();
        if ($used) return 0;

        // Has any prior paid booking? Then "first booking" is past.
        $hasPriorPaid = \App\Models\Booking::where('learner_id', $user->id)
            ->whereIn('payment_status', ['paid', 'refunded'])
            ->exists();
        if ($hasPriorPaid) return 0;

        // Expiry check
        $expiryDays = (int) SiteSetting::get('referral_expiry_days', 90);
        if ($expiryDays > 0 && $user->referred_at && now()->diffInDays($user->referred_at) > $expiryDays) {
            return 0;
        }

        // Flat amount takes precedence; else %
        $flat = (float) SiteSetting::get('referral_invitee_discount_amount', 0);
        if ($flat > 0) return min($flat, $orderTotal);

        $pct = (float) SiteSetting::get('referral_invitee_discount_pct', 0);
        if ($pct > 0) return round($orderTotal * ($pct / 100), 2);

        return 0;
    }

    public function referrerCreditAmount(): float
    {
        return (float) SiteSetting::get('referral_referrer_credit', 0);
    }

    /* ================================================================
     | Final order totals — single source of truth
     |================================================================ */

    /**
     * Compute order totals for a guest checkout (lesson hours + optional test package).
     * Returns full breakdown including discount tiers + coupon + referral.
     */
    public function calculateOrderTotals(
        int $hours,
        float $lessonPrice,
        bool $addTestPackage = false,
        ?float $testPackagePrice = null,
        ?string $couponCode = null,
        ?User $user = null,
    ): array {
        $subtotal = round($lessonPrice * $hours, 2);
        $discountPct = $this->discountPctForHours($hours);
        $bulkDiscount = round($subtotal * ($discountPct / 100), 2);
        $afterBulk = $subtotal - $bulkDiscount;

        $testPrice = 0;
        if ($addTestPackage) {
            $testPrice = $testPackagePrice !== null ? (float) $testPackagePrice : $this->defaultTestPackagePrice();
        }
        $afterTest = $afterBulk + $testPrice;

        // Coupon validation
        $couponDiscount = 0;
        $couponMsg = '';
        $couponObj = null;
        if ($couponCode) {
            $r = $this->validateCoupon($couponCode, $user, $afterTest);
            if ($r['ok']) {
                $couponDiscount = $r['discount'];
                $couponObj = $r['coupon'];
            } else {
                $couponMsg = $r['reason'];
            }
        }

        // Referral discount (mutually exclusive with coupon — admin's choice; we apply both if both qualify)
        $referralDiscount = 0;
        if ($user) {
            $referralDiscount = $this->referralDiscountFor($user, $afterTest);
        }

        $afterDiscounts = max(0, $afterTest - $couponDiscount - $referralDiscount);
        $platformFeePct = $this->platformFeePercent();
        $platformFee = round($afterDiscounts * ($platformFeePct / 100), 2);
        $total = round($afterDiscounts + $platformFee, 2);

        return [
            'hours' => $hours,
            'lesson_price' => $lessonPrice,
            'subtotal' => $subtotal,
            'bulk_discount_pct' => $discountPct,
            'bulk_discount_amount' => $bulkDiscount,
            'test_package_price' => $testPrice,
            'coupon_code' => $couponObj?->code,
            'coupon_discount' => $couponDiscount,
            'coupon_message' => $couponMsg,
            'referral_discount' => $referralDiscount,
            'platform_fee_pct' => $platformFeePct,
            'platform_fee' => $platformFee,
            'total' => $total,
        ];
    }
}

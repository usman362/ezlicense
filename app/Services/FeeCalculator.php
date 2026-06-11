<?php

namespace App\Services;

use App\Models\SiteSetting;

/**
 * Single source of truth for booking fee math.
 *
 * Fee model
 * ──────────
 *   - Instructor sets their own lesson price. They receive that price in full.
 *   - SecureLicence adds a flat platform service fee ($5 default) per lesson
 *     ON TOP of the instructor's price. This is platform revenue.
 *   - SecureLicence adds a processing fee ($2 default) per single-lesson booking
 *     to cover Stripe's per-transaction charge. WAIVED on packages of 5+ lessons
 *     because one big transaction means one Stripe fee instead of many small ones.
 *
 * Stripe charge per transaction (AU domestic cards, Nov 2024):
 *   - Percentage: 1.7%
 *   - Flat:      A$0.30
 *
 * The actual Stripe cost is whatever they take per transaction; the processing
 * fee charged to the learner is a simplified flat number ($2). On bulk packages
 * the learner saves that $2 × lessons, and the platform still profits because
 * Stripe's percentage cost stays small relative to the larger service-fee total.
 */
class FeeCalculator
{
    /** Number of lessons in a single order at which the processing fee is waived. */
    public const DEFAULT_PACKAGE_THRESHOLD = 5;

    public function __construct(
        protected ?float $serviceFee = null,
        protected ?float $processingFee = null,
        protected ?int $packageThreshold = null,
        protected ?float $stripePct = null,
        protected ?float $stripeFlat = null,
    ) {
        $this->serviceFee       ??= (float) SiteSetting::get('platform_service_fee', 5.00);
        $this->processingFee    ??= (float) SiteSetting::get('payment_processing_fee', 2.00);
        $this->packageThreshold ??= (int)   SiteSetting::get('processing_fee_waiver_threshold', self::DEFAULT_PACKAGE_THRESHOLD);
        $this->stripePct        ??= (float) SiteSetting::get('stripe_fee_percent', 1.7);
        $this->stripeFlat       ??= (float) SiteSetting::get('stripe_fee_flat', 0.30);
    }

    /* ─────────────────────────────────────────────────────────────────
     * Per-item calculation
     * ──────────────────────────────────────────────────────────────── */

    /**
     * Whether the processing fee is waived for this order size.
     */
    public function isPackageEligible(int $lessonCount): bool
    {
        return $lessonCount >= $this->packageThreshold;
    }

    /**
     * Service fee per lesson booking (never waived).
     */
    public function serviceFeePerItem(): float
    {
        return round($this->serviceFee, 2);
    }

    /**
     * Processing fee per lesson booking — $2 normally, $0 for 5+ lesson packages.
     */
    public function processingFeePerItem(int $lessonCount): float
    {
        return $this->isPackageEligible($lessonCount) ? 0.0 : round($this->processingFee, 2);
    }

    /* ─────────────────────────────────────────────────────────────────
     * Order-level totals
     * ──────────────────────────────────────────────────────────────── */

    /**
     * Calculate the full fee breakdown for an order.
     *
     * @param  float  $subtotal      Sum of instructor lesson prices (after discounts)
     * @param  int    $lessonCount   Number of lesson items in this order
     */
    public function calculate(float $subtotal, int $lessonCount): array
    {
        $perItemService    = $this->serviceFeePerItem();
        $perItemProcessing = $this->processingFeePerItem($lessonCount);

        $totalService    = round($perItemService * $lessonCount, 2);
        $totalProcessing = round($perItemProcessing * $lessonCount, 2);

        $total = round($subtotal + $totalService + $totalProcessing, 2);
        $stripeEstimate = $this->estimateStripeFee($total);

        // What the learner WOULD HAVE PAID on a single-lesson basis (no waiver) —
        // used in the UI to show "you save $X by booking a package".
        $hypotheticalSingleFee = round($this->processingFee * $lessonCount, 2);
        $savings = round($hypotheticalSingleFee - $totalProcessing, 2);

        return [
            'subtotal'                  => round($subtotal, 2),
            'lesson_count'              => $lessonCount,
            'package_eligible'          => $this->isPackageEligible($lessonCount),
            'service_fee_per_item'      => $perItemService,
            'processing_fee_per_item'   => $perItemProcessing,
            'service_fee_total'         => $totalService,
            'processing_fee_total'      => $totalProcessing,
            'platform_fee_total'        => round($totalService + $totalProcessing, 2),
            'savings_vs_single'         => $savings,
            'total'                     => $total,
            'stripe_fee_estimate'       => $stripeEstimate,
            'platform_net_estimate'     => round($totalService + $totalProcessing - $stripeEstimate, 2),
            'threshold'                 => $this->packageThreshold,
        ];
    }

    /**
     * Estimate Stripe's per-transaction cost — for the admin Fees Dashboard
     * and to populate `bookings.stripe_fee_estimate` so we can show real
     * platform profit per booking.
     */
    public function estimateStripeFee(float $chargeAmount): float
    {
        if ($chargeAmount <= 0) return 0.0;
        return round($chargeAmount * ($this->stripePct / 100) + $this->stripeFlat, 2);
    }

    /**
     * Allocate a single shared Stripe fee proportionally across multiple bookings
     * (so each booking row knows its share of the Stripe cost).
     *
     * @param  array<int, float>  $bookingAmounts
     * @return array<int, float>  Stripe fee share per index
     */
    public function allocateStripeFeeAcross(float $totalStripeFee, array $bookingAmounts): array
    {
        $sum = array_sum($bookingAmounts);
        if ($sum <= 0 || $totalStripeFee <= 0) {
            return array_map(fn () => 0.0, $bookingAmounts);
        }
        $shares = [];
        foreach ($bookingAmounts as $amount) {
            $shares[] = round($totalStripeFee * ($amount / $sum), 2);
        }
        return $shares;
    }
}

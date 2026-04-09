<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\InstructorPayout;
use App\Models\InstructorPayoutItem;
use App\Models\InstructorProfile;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\PayoutProcessed;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutService
{
    /**
     * Generate weekly payouts for all instructors.
     * Period: previous Sunday 00:00 → Saturday 23:59 (Australia/Sydney).
     *
     * @return int Number of payouts generated.
     */
    public function generateWeeklyPayouts(?Carbon $weekEnding = null): int
    {
        $tz = 'Australia/Sydney';

        // Default: last Saturday (most recent completed week)
        if (! $weekEnding) {
            $weekEnding = Carbon::now($tz)->previous(Carbon::SATURDAY);
        }
        $periodEnd = $weekEnding->copy()->endOfDay()->timezone($tz);
        $periodStart = $weekEnding->copy()->subDays(6)->startOfDay()->timezone($tz);

        // Convert to UTC for DB query
        $periodStartUtc = $periodStart->copy()->utc();
        $periodEndUtc = $periodEnd->copy()->utc();

        // Find all completed+paid bookings in this period NOT already assigned to a payout
        $bookings = Booking::where('status', Booking::STATUS_COMPLETED)
            ->whereIn('payment_status', [Booking::PAYMENT_PAID, 'paid'])
            ->whereNull('instructor_payout_id')
            ->whereBetween('scheduled_at', [$periodStartUtc, $periodEndUtc])
            ->with('instructorProfile')
            ->get();

        if ($bookings->isEmpty()) {
            return 0;
        }

        // Group by instructor_profile_id
        $grouped = $bookings->groupBy(function (Booking $b) {
            return $b->instructor_profile_id ?? $b->instructorProfile?->id;
        })->filter(fn ($group, $key) => $key !== null);

        $serviceFee = $this->getServiceFee();
        $processingFee = $this->getProcessingFee();
        $minPayout = (float) SiteSetting::get('minimum_payout_amount', 1.00);
        $count = 0;

        foreach ($grouped as $profileId => $instructorBookings) {
            $profile = InstructorProfile::find($profileId);
            if (! $profile) {
                continue;
            }

            // Check for duplicate: skip if payout already exists for this period + instructor
            $exists = InstructorPayout::where('instructor_profile_id', $profileId)
                ->where('period_start', $periodStart)
                ->where('period_end', $periodEnd)
                ->exists();
            if ($exists) {
                continue;
            }

            $gstRegistered = (bool) $profile->gst_registered;
            $totalFeePerBooking = $serviceFee + $processingFee;

            $grossTotal = 0;
            $serviceTotal = 0;
            $processingTotal = 0;
            $gstTotal = 0;
            $netTotal = 0;
            $items = [];

            foreach ($instructorBookings as $booking) {
                $gross = (float) $booking->amount;
                $gstOnFees = $gstRegistered ? round($totalFeePerBooking / 11, 2) : 0;
                $net = max(round($gross - $totalFeePerBooking, 2), 0);

                $grossTotal += $gross;
                $serviceTotal += $serviceFee;
                $processingTotal += $processingFee;
                $gstTotal += $gstOnFees;
                $netTotal += $net;

                $items[] = [
                    'booking_id'     => $booking->id,
                    'gross_amount'   => $gross,
                    'service_fee'    => $serviceFee,
                    'processing_fee' => $processingFee,
                    'gst_on_fees'    => $gstOnFees,
                    'net_amount'     => $net,
                ];
            }

            // Skip if below minimum payout amount
            if ($netTotal < $minPayout && $netTotal > 0) {
                continue; // Will be picked up next week
            }

            DB::transaction(function () use (
                $profileId, $periodStart, $periodEnd,
                $instructorBookings, $items,
                $grossTotal, $serviceTotal, $processingTotal, $gstTotal, $netTotal,
            ) {
                $payout = InstructorPayout::create([
                    'instructor_profile_id' => $profileId,
                    'reference'             => InstructorPayout::generateReference($periodEnd),
                    'period_start'          => $periodStart,
                    'period_end'            => $periodEnd,
                    'bookings_count'        => count($items),
                    'gross_amount'          => round($grossTotal, 2),
                    'service_fee_total'     => round($serviceTotal, 2),
                    'processing_fee_total'  => round($processingTotal, 2),
                    'gst_on_fees'           => round($gstTotal, 2),
                    'net_amount'            => round($netTotal, 2),
                    'status'                => InstructorPayout::STATUS_PENDING,
                ]);

                foreach ($items as $item) {
                    InstructorPayoutItem::create([
                        'instructor_payout_id' => $payout->id,
                        ...$item,
                        'created_at' => now(),
                    ]);
                }

                // Link bookings to this payout
                Booking::whereIn('id', $instructorBookings->pluck('id'))
                    ->update(['instructor_payout_id' => $payout->id]);
            });

            $count++;
        }

        Log::info("PayoutService: Generated {$count} weekly payouts for period {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");
        return $count;
    }

    /**
     * Calculate fees for a single booking.
     */
    public function calculateBookingFees(float $bookingAmount, bool $gstRegistered = false): array
    {
        $serviceFee = $this->getServiceFee();
        $processingFee = $this->getProcessingFee();
        $totalFees = $serviceFee + $processingFee;
        $gstOnFees = $gstRegistered ? round($totalFees / 11, 2) : 0;
        $net = max(round($bookingAmount - $totalFees, 2), 0);

        return [
            'service_fee'    => $serviceFee,
            'processing_fee' => $processingFee,
            'total_fees'     => $totalFees,
            'gst_on_fees'    => $gstOnFees,
            'net_amount'     => $net,
        ];
    }

    public function approvePayout(InstructorPayout $payout, User $admin): void
    {
        if (! $payout->canApprove()) {
            throw new \RuntimeException('Payout cannot be approved in its current state.');
        }

        $profile = $payout->instructorProfile;
        if (! $profile->bank_account_number || ! $profile->bank_bsb) {
            throw new \RuntimeException('Instructor has not submitted bank details.');
        }

        $payout->update([
            'status'      => InstructorPayout::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
    }

    public function markAsPaid(InstructorPayout $payout, ?string $paymentReference = null): void
    {
        if (! $payout->canMarkPaid()) {
            throw new \RuntimeException('Payout cannot be marked as paid in its current state.');
        }

        $payout->update([
            'status'            => InstructorPayout::STATUS_PAID,
            'paid_at'           => now(),
            'payment_reference' => $paymentReference,
        ]);

        // Notify the instructor
        try {
            $instructor = $payout->instructorProfile?->user;
            if ($instructor) {
                $instructor->notify(new PayoutProcessed($payout));
            }
        } catch (\Throwable $e) {
            Log::warning("PayoutService: Notification failed for payout #{$payout->id}: {$e->getMessage()}");
        }
    }

    public function markAsFailed(InstructorPayout $payout, string $reason): void
    {
        $payout->update([
            'status'         => InstructorPayout::STATUS_FAILED,
            'failure_reason' => $reason,
        ]);
    }

    public function bulkApprove(array $ids, User $admin): int
    {
        $count = 0;
        $payouts = InstructorPayout::whereIn('id', $ids)->pending()->with('instructorProfile')->get();
        foreach ($payouts as $payout) {
            try {
                $this->approvePayout($payout, $admin);
                $count++;
            } catch (\Throwable $e) {
                Log::warning("PayoutService: Bulk approve skipped #{$payout->id}: {$e->getMessage()}");
            }
        }
        return $count;
    }

    public function bulkMarkPaid(array $ids, ?string $paymentReference = null): int
    {
        $count = 0;
        $payouts = InstructorPayout::whereIn('id', $ids)->approved()->get();
        foreach ($payouts as $payout) {
            try {
                $this->markAsPaid($payout, $paymentReference);
                $count++;
            } catch (\Throwable $e) {
                Log::warning("PayoutService: Bulk mark-paid skipped #{$payout->id}: {$e->getMessage()}");
            }
        }
        return $count;
    }

    /**
     * Financial year statement for an instructor.
     * Australian FY: 1 July $year → 30 June ($year+1).
     */
    public function getFinancialYearStatement(InstructorProfile $profile, int $fyStartYear): array
    {
        $fyStart = Carbon::create($fyStartYear, 7, 1, 0, 0, 0, 'Australia/Sydney');
        $fyEnd = Carbon::create($fyStartYear + 1, 6, 30, 23, 59, 59, 'Australia/Sydney');

        $payouts = InstructorPayout::forInstructor($profile->id)
            ->where('period_start', '>=', $fyStart->utc())
            ->where('period_end', '<=', $fyEnd->utc())
            ->where('status', InstructorPayout::STATUS_PAID)
            ->orderBy('period_start')
            ->get();

        $bookings = Booking::where('instructor_id', $profile->user_id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->whereBetween('scheduled_at', [$fyStart->utc(), $fyEnd->utc()])
            ->get();

        return [
            'instructor_name' => $profile->user?->name,
            'abn'             => $profile->abn,
            'gst_registered'  => (bool) $profile->gst_registered,
            'fy_label'        => $fyStartYear . '/' . ($fyStartYear + 1 - 2000),
            'fy_start'        => $fyStart->format('d M Y'),
            'fy_end'          => $fyEnd->format('d M Y'),

            'total_bookings'       => $bookings->count(),
            'gross_earnings'       => round($bookings->sum('amount'), 2),
            'total_service_fees'   => round($payouts->sum('service_fee_total'), 2),
            'total_processing_fees' => round($payouts->sum('processing_fee_total'), 2),
            'total_gst_on_fees'    => round($payouts->sum('gst_on_fees'), 2),
            'total_deductions'     => round($payouts->sum('service_fee_total') + $payouts->sum('processing_fee_total'), 2),
            'net_earnings'         => round($payouts->sum('net_amount'), 2),
            'payouts_count'        => $payouts->count(),

            'payouts' => $payouts->map(fn (InstructorPayout $p) => [
                'reference'    => $p->reference,
                'period'       => $p->periodLabel(),
                'gross'        => (float) $p->gross_amount,
                'deductions'   => $p->totalDeductions(),
                'net'          => (float) $p->net_amount,
                'paid_at'      => $p->paid_at?->format('d M Y'),
            ]),
        ];
    }

    // ── Private helpers ──────────────────────────

    private function getServiceFee(): float
    {
        return (float) SiteSetting::get('platform_service_fee', 5.00);
    }

    private function getProcessingFee(): float
    {
        return (float) SiteSetting::get('payment_processing_fee', 2.00);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\FeeCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Real-time P&L view for platform owners.
 *
 * For every paid booking in the selected window we show:
 *   - Learner paid:    amount + service_fee + processing_fee
 *   - Instructor got:  instructor_net_amount (their full price)
 *   - Stripe took:     stripe_fee_estimate (1.7% + $0.30 per AU domestic charge)
 *   - Platform net:    (service + processing) − stripe_fee_estimate
 *
 * Helps answer: "after Stripe, how much actually landed in our wallet?"
 */
class FeesDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default window: this month
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth();
        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $query = Booking::with(['learner:id,name,email', 'instructor:id,name'])
            ->where('payment_status', Booking::PAYMENT_PAID)
            ->whereBetween('updated_at', [$from, $to])
            ->orderByDesc('updated_at');

        $bookings = $query->paginate(50)->withQueryString();

        // Aggregate totals from the same query (without pagination)
        $aggregateQuery = (clone $query);
        $aggregateQuery->getQuery()->orders = []; // drop order-by for sum performance
        $totals = $aggregateQuery->selectRaw('
                COUNT(*) AS total_bookings,
                COALESCE(SUM(amount), 0) AS instructor_total,
                COALESCE(SUM(platform_fee), 0) AS service_fee_total,
                COALESCE(SUM(processing_fee), 0) AS processing_fee_total,
                COALESCE(SUM(stripe_fee_estimate), 0) AS stripe_total
            ')->first();

        $instructorTotal   = (float) $totals->instructor_total;
        $serviceFeeTotal   = (float) $totals->service_fee_total;
        $processingTotal   = (float) $totals->processing_fee_total;
        $stripeTotal       = (float) $totals->stripe_total;
        $platformFeeTotal  = $serviceFeeTotal + $processingTotal;
        $platformNet       = $platformFeeTotal - $stripeTotal;
        $learnerTotal      = $instructorTotal + $platformFeeTotal;

        return view('admin.fees-dashboard.index', [
            'bookings' => $bookings,
            'from'     => $from,
            'to'       => $to,
            'totals'   => [
                'bookings'        => (int) $totals->total_bookings,
                'learner_paid'    => $learnerTotal,
                'instructor_got'  => $instructorTotal,
                'service_fee'     => $serviceFeeTotal,
                'processing_fee'  => $processingTotal,
                'platform_fee'    => $platformFeeTotal,
                'stripe_took'     => $stripeTotal,
                'platform_net'    => $platformNet,
                'margin_pct'      => $learnerTotal > 0 ? round($platformNet / $learnerTotal * 100, 1) : 0,
            ],
        ]);
    }

    /**
     * CSV export of the same data set (no pagination).
     */
    public function export(Request $request): StreamedResponse
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth();
        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $filename = sprintf('securelicence-fees-%s-to-%s.csv', $from->format('Ymd'), $to->format('Ymd'));

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Booking ID', 'Date Paid', 'Learner', 'Instructor',
                'Amount', 'Service Fee', 'Processing Fee', 'Stripe Fee',
                'Learner Paid', 'Instructor Got', 'Platform Net',
            ]);

            Booking::with(['learner:id,name', 'instructor:id,name'])
                ->where('payment_status', Booking::PAYMENT_PAID)
                ->whereBetween('updated_at', [$from, $to])
                ->orderBy('updated_at')
                ->chunk(200, function ($chunk) use ($out) {
                    foreach ($chunk as $b) {
                        $amount = (float) $b->amount;
                        $service = (float) ($b->platform_fee ?? 0);
                        $processing = (float) ($b->processing_fee ?? 0);
                        $stripe = (float) ($b->stripe_fee_estimate ?? 0);
                        $learnerPaid = $amount + $service + $processing;
                        $platformNet = $service + $processing - $stripe;
                        fputcsv($out, [
                            $b->id,
                            $b->updated_at?->format('Y-m-d H:i'),
                            $b->learner?->name ?? '—',
                            $b->instructor?->name ?? '—',
                            number_format($amount, 2),
                            number_format($service, 2),
                            number_format($processing, 2),
                            number_format($stripe, 2),
                            number_format($learnerPaid, 2),
                            number_format($amount, 2),
                            number_format($platformNet, 2),
                        ]);
                    }
                });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}

<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Learner-facing receipts.
 *
 * Every booking becomes a "receipt" that the learner can view and download
 * as a PDF. The receipt content reflects the booking's current state:
 *   - PAID            → standard tax invoice
 *   - COMPLETED       → tax invoice marked "Service rendered"
 *   - CANCELLED       → cancellation notice (with refund line if any)
 *   - PENDING         → pro forma (awaiting payment confirmation)
 *
 * No new model is needed — each receipt is derived from the booking.
 */
class ReceiptsController extends Controller
{
    /**
     * List all receipts for the authenticated learner.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user && $user->isLearner(), 403);

        $query = Booking::where('learner_id', $user->id)
            ->with(['instructor:id,name', 'suburb.state']);

        // Filter by tab — paid, refunded, cancelled, all (default)
        $filter = $request->input('filter', 'all');
        if ($filter === 'paid') {
            $query->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED, Booking::STATUS_INSTRUCTOR_ARRIVED, Booking::STATUS_IN_PROGRESS])
                  ->whereIn('payment_status', [Booking::PAYMENT_PAID]);
        } elseif ($filter === 'refunded') {
            $query->whereNotNull('refund_amount')->where('refund_amount', '>', 0);
        } elseif ($filter === 'cancelled') {
            $query->where('status', Booking::STATUS_CANCELLED);
        }

        $bookings = $query->orderByDesc('scheduled_at')->paginate(15)->withQueryString();

        // Add aggregate stats for the header
        $totalPaid = (float) Booking::where('learner_id', $user->id)
            ->where('payment_status', Booking::PAYMENT_PAID)
            ->sum('amount');
        $totalRefunded = (float) Booking::where('learner_id', $user->id)
            ->whereNotNull('refund_amount')
            ->sum('refund_amount');
        $totalCount = Booking::where('learner_id', $user->id)->count();

        return view('learner.pages.receipts', [
            'bookings'      => $bookings,
            'filter'        => $filter,
            'totalPaid'     => $totalPaid,
            'totalRefunded' => $totalRefunded,
            'totalCount'    => $totalCount,
        ]);
    }

    /**
     * Show one receipt (HTML preview).
     */
    public function show(Booking $booking): View
    {
        $this->guardBookingAccess($booking);
        $receipt = $this->buildReceiptArray($booking);
        return view('learner.pages.receipt-detail', compact('booking', 'receipt'));
    }

    /**
     * Download a receipt as a PDF.
     */
    public function download(Booking $booking): Response
    {
        $this->guardBookingAccess($booking);
        $receipt = $this->buildReceiptArray($booking);

        $pdf = Pdf::loadView('pdf.receipt', ['receipt' => $receipt])
            ->setPaper('a4', 'portrait');

        return $pdf->download($receipt['number'] . '.pdf');
    }

    /**
     * Build the structured array consumed by the PDF view + the HTML detail page.
     * One source of truth for both presentations.
     */
    public function buildReceiptArray(Booking $b): array
    {
        $b->loadMissing(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state']);

        // Status interpretation
        [$statusLabel, $statusClass, $docTitle] = $this->resolveStatus($b);

        // Money calc
        $amount = (float) $b->amount;
        $couponDiscount = (float) ($b->coupon_discount_amount ?? 0);
        $subtotal = max(0, $amount - $couponDiscount);
        // We treat the listed amount as GST-inclusive (Australia 10%)
        $gstAmount = round($subtotal - ($subtotal / 1.1), 2);
        $totalPaid = $subtotal;
        $refundAmount = (float) ($b->refund_amount ?? 0);
        $cancellationFee = $refundAmount > 0 ? max(0, $totalPaid - $refundAmount) : 0;
        $netToLearner = $refundAmount;

        // Pretty location
        $location = $b->suburb
            ? trim(implode(' ', array_filter([
                $b->suburb->name,
                $b->suburb->postcode,
                $b->suburb->state?->code,
            ])))
            : null;

        return [
            'number'          => $this->receiptNumber($b),
            'doc_title'       => $docTitle,
            'issued_at'       => now(),
            'booking_id'      => $b->id,
            'status_label'    => $statusLabel,
            'status_class'    => $statusClass,
            'paid_at'         => $b->payment_status === Booking::PAYMENT_PAID ? ($b->updated_at) : null,
            'cancelled_at'    => $b->cancelled_at,
            'refunded_at'     => $b->refunded_at,

            // Parties
            'learner' => [
                'name'  => $b->learner?->name,
                'email' => $b->learner?->email,
                'phone' => $b->learner?->phone,
            ],
            'instructor' => [
                'name'  => $b->instructor?->name,
                'email' => $b->instructor?->email,
                'phone' => $b->instructor?->phone,
            ],

            // Booking detail
            'service_label'    => $b->type === Booking::TYPE_TEST_PACKAGE ? 'Driving Test Package' : 'Driving Lesson',
            'scheduled_at'     => $b->scheduled_at,
            'duration_minutes' => (int) $b->duration_minutes,
            'transmission'     => $b->transmission ?? 'auto',
            'location'         => $location,

            // Money
            'amount'                    => $amount,
            'coupon_code'               => $b->coupon_code,
            'coupon_discount'           => $couponDiscount,
            'subtotal'                  => $subtotal,
            'gst_amount'                => $gstAmount,
            'total_paid'                => $totalPaid,
            'payment_method_label'      => $this->paymentMethodLabel($b->payment_method),
            'refund_amount'             => $refundAmount,
            'refund_method_label'       => $this->refundMethodLabel($b->refund_method),
            'cancellation_fee_retained' => $cancellationFee,
            'net_to_learner'            => $netToLearner,

            // Reasons / notes
            'cancellation_reason'  => $b->cancellation_reason,
            'cancellation_message' => $b->cancellation_message,
            'refund_reason'        => $b->refund_reason,
            'refund_reference'     => $b->refund_reference,

            'support_email' => SiteSetting::get('support_email', 'support@securelicence.com'),
        ];
    }

    /* ───────── Helpers ───────── */

    /** Authorise: only the booking's learner (or admin) can view its receipt. */
    private function guardBookingAccess(Booking $booking): void
    {
        $user = Auth::user();
        if (! $user) abort(401);
        if ($user->isAdmin()) return;
        if ($user->id !== $booking->learner_id) {
            abort(403, 'You do not have access to this receipt.');
        }
    }

    /** Receipt number: SL-{YYYYMMDD}-{padded booking id} — matches PaymentReceipt notification. */
    private function receiptNumber(Booking $b): string
    {
        $datePart = $b->scheduled_at?->format('Ymd') ?? now()->format('Ymd');
        return 'SL-' . $datePart . '-' . str_pad((string) $b->id, 6, '0', STR_PAD_LEFT);
    }

    /** Returns [label, css-class-suffix, doc-title]. */
    private function resolveStatus(Booking $b): array
    {
        if ($b->status === Booking::STATUS_CANCELLED) {
            if ((float) ($b->refund_amount ?? 0) > 0) {
                return ['Cancelled — Refunded', 'refunded', 'Cancellation & Refund Notice'];
            }
            return ['Cancelled — No refund', 'cancelled', 'Cancellation Notice'];
        }
        if ($b->status === Booking::STATUS_COMPLETED) {
            return ['Completed', 'completed', 'Tax Invoice / Receipt'];
        }
        if ($b->payment_status === Booking::PAYMENT_PAID) {
            return ['Paid', 'paid', 'Tax Invoice / Receipt'];
        }
        if ($b->payment_status === Booking::PAYMENT_REFUNDED) {
            return ['Refunded', 'refunded', 'Refund Notice'];
        }
        return ['Awaiting payment', 'pending', 'Pro Forma Invoice'];
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'card'   => 'card',
            'paypal' => 'PayPal',
            'wallet' => 'wallet',
            'manual' => 'manual',
            default  => $method ?: '',
        };
    }

    private function refundMethodLabel(?string $method): ?string
    {
        return match ($method) {
            'wallet'           => 'Wallet credit',
            'original_payment' => 'Original payment method',
            'manual_bank'      => 'Bank transfer',
            default            => $method,
        };
    }
}

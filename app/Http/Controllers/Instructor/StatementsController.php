<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorPayout;
use App\Models\SiteSetting;
use App\Services\StatementPeriodService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Instructor-facing weekly / fortnightly / monthly statements.
 *
 * A statement is generated on-demand from completed booking data in a period
 * (defined by instructor's payout_frequency setting). If an InstructorPayout
 * record exists for that period, the payment status is reflected from it;
 * otherwise the statement shows "pending" payout status.
 */
class StatementsController extends Controller
{
    public function __construct(
        protected StatementPeriodService $periodService,
    ) {}

    /**
     * List recent statement periods (12 by default).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $profile = $user?->instructorProfile;
        abort_unless($user && $user->isInstructor() && $profile, 403);

        $count = (int) min(24, max(4, $request->input('count', 12)));
        $periods = $this->periodService->recent($profile, $count);

        // For each period, compute a quick summary (no per-booking detail yet)
        $summaries = $periods->map(function (array $p) use ($profile) {
            return array_merge($p, $this->summariseForPeriod($profile, $p['start'], $p['end']));
        });

        return view('instructor.pages.statements', [
            'periods'   => $summaries,
            'frequency' => $profile->payout_frequency ?? 'weekly',
        ]);
    }

    /**
     * View a single statement (HTML).
     *
     * @param  string  $key  YYYY-MM-DD start of period
     */
    public function show(string $key): View
    {
        $user = Auth::user();
        $profile = $user?->instructorProfile;
        abort_unless($user && $user->isInstructor() && $profile, 403);

        $period = $this->periodService->fromKey($profile, $key);
        abort_unless($period, 404);

        $statement = $this->buildStatement($profile, $period);

        return view('instructor.pages.statement-detail', compact('statement'));
    }

    /**
     * Download a single statement as a PDF.
     */
    public function download(string $key): Response
    {
        $user = Auth::user();
        $profile = $user?->instructorProfile;
        abort_unless($user && $user->isInstructor() && $profile, 403);

        $period = $this->periodService->fromKey($profile, $key);
        abort_unless($period, 404);

        $statement = $this->buildStatement($profile, $period);

        $pdf = Pdf::loadView('pdf.statement', ['statement' => $statement])
            ->setPaper('a4', 'portrait');

        return $pdf->download($statement['reference'] . '.pdf');
    }

    /* ─────────────────────────────────────────────────────────────────────
     * Builders
     * ──────────────────────────────────────────────────────────────────── */

    /**
     * Compact summary used in the listing — totals only.
     */
    private function summariseForPeriod($profile, Carbon $start, Carbon $end): array
    {
        $bookings = Booking::where('instructor_id', $profile->user_id)
            ->whereIn('status', [Booking::STATUS_COMPLETED])
            ->whereBetween('scheduled_at', [$start->copy()->utc(), $end->copy()->utc()])
            ->get(['amount', 'instructor_net_amount', 'duration_minutes']);

        $count = $bookings->count();
        $gross = (float) $bookings->sum('amount');
        $net = (float) $bookings->sum(fn ($b) => (float) ($b->instructor_net_amount ?? 0));
        $mins = (int) $bookings->sum(fn ($b) => (int) ($b->duration_minutes ?? 60));

        // Check payout row state
        $payout = InstructorPayout::where('instructor_profile_id', $profile->id)
            ->whereDate('period_start', $start->copy()->toDateString())
            ->first();

        return [
            'bookings_count'  => $count,
            'gross_amount'    => $gross,
            'net_amount'      => $net,
            'lesson_minutes'  => $mins,
            'payout_status'   => $payout?->status ?? ($count > 0 ? 'pending' : 'no_bookings'),
            'payout_paid_at'  => $payout?->paid_at,
        ];
    }

    /**
     * Full statement detail (used by HTML view + PDF view).
     */
    private function buildStatement($profile, array $period): array
    {
        $start = $period['start'];
        $end = $period['end'];
        $user = $profile->user;

        // Completed bookings — these earn payout
        $completed = Booking::where('instructor_id', $profile->user_id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->whereBetween('scheduled_at', [$start->copy()->utc(), $end->copy()->utc()])
            ->with('learner:id,name')
            ->orderBy('scheduled_at')
            ->get();

        // Cancelled bookings within the period (visibility only, no payout)
        $cancelled = Booking::where('instructor_id', $profile->user_id)
            ->where('status', Booking::STATUS_CANCELLED)
            ->whereBetween('scheduled_at', [$start->copy()->utc(), $end->copy()->utc()])
            ->with('learner:id,name')
            ->orderBy('scheduled_at')
            ->get();

        $serviceFee = (float) SiteSetting::get('platform_service_fee', 5.00);
        $processingFee = (float) SiteSetting::get('payment_processing_fee', 2.00);
        $feePerBooking = $serviceFee + $processingFee;

        $items = $completed->map(function (Booking $b) use ($feePerBooking) {
            $gross = (float) $b->amount;
            $net = (float) ($b->instructor_net_amount ?? max($gross - $feePerBooking, 0));
            return [
                'booking_id'     => $b->id,
                'scheduled_at'   => $b->scheduled_at,
                'learner_name'   => $b->learner?->name ?? 'Learner',
                'type'           => $b->type === Booking::TYPE_TEST_PACKAGE ? 'Test Package' : 'Lesson',
                'duration_mins'  => (int) ($b->duration_minutes ?? 60),
                'gross'          => $gross,
                'fees'           => $feePerBooking,
                'net'            => $net,
            ];
        });

        $totals = [
            'bookings'    => $completed->count(),
            'cancelled'   => $cancelled->count(),
            'lesson_hrs'  => round($completed->sum(fn ($b) => (int) ($b->duration_minutes ?? 60)) / 60, 1),
            'gross'       => (float) $completed->sum('amount'),
            'fees'        => $feePerBooking * $completed->count(),
            'net'         => (float) $items->sum('net'),
        ];

        // Linked payout row (if generated)
        $payout = InstructorPayout::where('instructor_profile_id', $profile->id)
            ->whereDate('period_start', $start->copy()->toDateString())
            ->first();

        $reference = $payout?->reference
            ?? ('SL-STMT-' . $start->format('Ymd') . '-' . str_pad((string) $profile->id, 4, '0', STR_PAD_LEFT));

        return [
            'reference'      => $reference,
            'period_label'   => $period['label'],
            'period_key'     => $period['key'],
            'period_start'   => $start,
            'period_end'     => $end,
            'frequency'      => $period['frequency'],
            'is_current'     => $period['is_current'],
            'issued_at'      => now(),

            // Instructor party block
            'instructor' => [
                'name'           => $user?->name,
                'email'          => $user?->email,
                'phone'          => $user?->phone,
                'business_name'  => $profile->business_name,
                'abn'            => $profile->abn,
                'billing_address'=> $profile->billing_address,
                'gst_registered' => (bool) $profile->gst_registered,
                'bank_bsb'       => $profile->bank_bsb,
                'bank_account_masked' => $profile->bank_account_number
                    ? '****' . substr($profile->bank_account_number, -4)
                    : null,
            ],

            // Lessons table
            'items'     => $items->all(),
            'cancelled_count' => $cancelled->count(),
            'totals'    => $totals,

            // Fees breakdown
            'fee_breakdown' => [
                'service_fee_per_booking'    => $serviceFee,
                'processing_fee_per_booking' => $processingFee,
                'total_fees'                 => $totals['fees'],
            ],

            // Payout status
            'payout' => [
                'exists'          => (bool) $payout,
                'status'          => $payout?->status ?? ($totals['bookings'] > 0 ? 'pending' : 'no_bookings'),
                'status_label'    => $this->payoutStatusLabel($payout?->status, $totals['bookings'] > 0, $period['is_current']),
                'paid_at'         => $payout?->paid_at,
                'payment_ref'     => $payout?->payment_reference,
                'failure_reason'  => $payout?->failure_reason,
            ],

            'support_email' => SiteSetting::get('support_email', 'support@securelicence.com'),
        ];
    }

    private function payoutStatusLabel(?string $status, bool $hasBookings, bool $isCurrent): string
    {
        if (! $hasBookings) return 'No bookings in this period';
        if ($isCurrent) return 'In progress — payout after period ends';
        return match ($status) {
            'paid'       => 'Paid',
            'approved'   => 'Approved — awaiting transfer',
            'processing' => 'Processing transfer',
            'failed'     => 'Failed — please contact support',
            'pending', null => 'Pending — payout will be generated soon',
            default      => ucfirst($status),
        };
    }
}

<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Report data for Summary, This Financial Year, and FY 2024/25.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $profile = $user?->instructorProfile;
        if (! $profile) {
            return response()->json(['data' => $this->emptyReport()]);
        }

        $instructorId = $user->id;
        $now = Carbon::now();
        $ninetyDaysAgo = $now->copy()->subDays(90);

        $bookings = Booking::where('instructor_id', $instructorId)->with('learner:id,name');

        $completed = (clone $bookings)->where('status', Booking::STATUS_COMPLETED);
        $cancelled = (clone $bookings)->where('status', Booking::STATUS_CANCELLED);
        $confirmed = (clone $bookings)->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PROPOSED])->where('scheduled_at', '>', $now);

        $completed90 = (clone $completed)->where('scheduled_at', '>=', $ninetyDaysAgo);
        $cancelled90 = (clone $cancelled)->where('cancelled_at', '>=', $ninetyDaysAgo);

        $totalEarnings = (float) $completed->sum('amount');
        $earnings90 = (float) $completed90->sum('amount');
        $hours90 = $completed90->sum(DB::raw('COALESCE(duration_minutes, 60)')) / 60;
        $completedCount90 = $completed90->count();
        $cancelledCount90 = $cancelled90->count();
        $totalCount90 = $completedCount90 + $cancelledCount90;
        $cancellationRate = $totalCount90 > 0 ? round(100 * $cancelledCount90 / $totalCount90, 1) : 0;

        $uniqueLearners90 = (clone $completed90)->distinct('learner_id')->count('learner_id');
        $bookingHoursPerLearner = $uniqueLearners90 > 0 ? round($hours90 / $uniqueLearners90, 2) : 0;

        $avgRating = (float) Review::where('instructor_id', $instructorId)->avg('rating');
        $upcomingAmount = (float) $confirmed->sum('amount');

        $fyCurrent = $this->financialYear($now);
        $fyPrevious = $fyCurrent - 1;
        $fyStartCurrent = Carbon::createFromDate($fyCurrent - 1, 7, 1);
        $fyEndCurrent = Carbon::createFromDate($fyCurrent, 6, 30)->endOfDay();
        $fyStartPrevious = Carbon::createFromDate($fyPrevious - 1, 7, 1);
        $fyEndPrevious = Carbon::createFromDate($fyPrevious, 6, 30)->endOfDay();

        $fytdCurrent = (float) (clone $completed)->whereBetween('scheduled_at', [$fyStartCurrent, $fyEndCurrent])->sum('amount');
        $fytdPrevious = (float) (clone $completed)->whereBetween('scheduled_at', [$fyStartPrevious, $fyEndPrevious])->sum('amount');

        $earningsByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $earningsByMonth[] = [
                'month' => $start->format('M Y'),
                'amount' => (float) (clone $completed)->whereBetween('scheduled_at', [$start, $end])->sum('amount'),
            ];
        }

        $pendingPayoutRows = (clone $bookings)
            ->where('status', Booking::STATUS_COMPLETED)
            ->where('scheduled_at', '>=', $fyStartCurrent)
            ->where('scheduled_at', '<=', $fyEndCurrent)
            ->orderBy('scheduled_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'learner_name' => $b->learner?->name ?? 'Learner',
                'scheduled_at' => $b->scheduled_at->format('j M Y, g:i A'),
                'payout' => (float) $b->amount,
                'lesson_id' => '#'.$b->id,
            ]);

        $fortnightlyCurrent = $this->fortnightlyReports($instructorId, $fyStartCurrent, $fyEndCurrent);
        $fortnightlyPrevious = $this->fortnightlyReports($instructorId, $fyStartPrevious, $fyEndPrevious);

        // Calculate real payout schedule based on instructor's payout_frequency
        $payoutFreq = $profile->payout_frequency ?? 'fortnightly';
        $nextPayoutDate = match ($payoutFreq) {
            'weekly'            => $now->copy()->next('Monday'),
            'every_four_weeks'  => $now->copy()->next('Monday')->addWeeks(max(0, 3 - ($now->weekOfYear % 4))),
            default             => $now->copy()->next('Monday')->addWeeks($now->weekOfYear % 2 === 0 ? 0 : 1),
        };

        // Estimate next payout from recent completed bookings since last payout date
        $lastPayoutDate = match ($payoutFreq) {
            'weekly'            => $now->copy()->previous('Monday'),
            'every_four_weeks'  => $now->copy()->subWeeks(4)->startOfWeek(),
            default             => $now->copy()->subWeeks(2)->startOfWeek(),
        };
        $nextPayoutAmount = (float) Booking::where('instructor_id', $instructorId)
            ->where('status', Booking::STATUS_COMPLETED)
            ->where('scheduled_at', '>=', $lastPayoutDate)
            ->where('scheduled_at', '<=', $now)
            ->sum('amount');

        // Previous payout: the period before lastPayoutDate
        $prevPayoutStart = match ($payoutFreq) {
            'weekly'            => $lastPayoutDate->copy()->subWeek(),
            'every_four_weeks'  => $lastPayoutDate->copy()->subWeeks(4),
            default             => $lastPayoutDate->copy()->subWeeks(2),
        };
        $previousPayoutAmount = (float) Booking::where('instructor_id', $instructorId)
            ->where('status', Booking::STATUS_COMPLETED)
            ->where('scheduled_at', '>=', $prevPayoutStart)
            ->where('scheduled_at', '<', $lastPayoutDate)
            ->sum('amount');

        // Credits held: from learner wallets with bookings to this instructor
        $creditsHeld = (float) Booking::where('instructor_id', $instructorId)
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PROPOSED])
            ->where('scheduled_at', '>', $now)
            ->sum('amount');

        return response()->json([
            'data' => [
                'summary' => [
                    'earnings' => $totalEarnings,
                    'earnings_display' => '$'.number_format($totalEarnings, 2),
                    'next_payout_amount' => $nextPayoutAmount,
                    'next_payout_date' => $nextPayoutDate->format('j M'),
                    'cancellation_rate' => $cancellationRate,
                    'booking_hours_per_learner' => $bookingHoursPerLearner,
                    'learner_rating' => round($avgRating, 1),
                    'earnings_by_month' => $earningsByMonth,
                    'next_payout' => $nextPayoutAmount,
                    'previous_payout' => $previousPayoutAmount,
                    'fytd_payout' => $fytdCurrent,
                    'fytd_fy' => ($fyCurrent - 1).'-'.substr((string) $fyCurrent, 2),
                    'all_time_earnings' => $totalEarnings,
                    'ave_weekly_earnings_90' => $completedCount90 > 0 ? round($earnings90 / 13, 2) : 0,
                    'ave_earnings_per_hour_90' => $hours90 > 0 ? round($earnings90 / $hours90, 2) : 0,
                    'upcoming_bookings' => $upcomingAmount,
                    'credits_held' => $creditsHeld,
                    'searches_in_area' => 0, // Requires search tracking implementation
                    'test_packages' => (clone $bookings)->where('type', Booking::TYPE_TEST_PACKAGE)->where('status', Booking::STATUS_COMPLETED)->count(),
                    'total_booking_hrs' => (int) $completed->sum(DB::raw('COALESCE(duration_minutes, 60)')) / 60,
                    'learners_count' => (clone $bookings)->distinct('learner_id')->count('learner_id'),
                    'upcoming_hrs_booked' => (int) $confirmed->sum(DB::raw('COALESCE(duration_minutes, 60)')) / 60,
                    'lesson_hrs_completed' => (int) $completed->sum(DB::raw('COALESCE(duration_minutes, 60)')) / 60,
                ],
                'pending_payout' => [
                    'next_date' => $nextPayoutDate->format('j M Y'),
                    'rows' => $pendingPayoutRows,
                ],
                'fy_current' => [
                    'label' => ($fyCurrent - 1).'/'.substr((string) $fyCurrent, 2),
                    'period' => $fyStartCurrent->format('j M Y').' - '.$fyEndCurrent->format('j M Y'),
                    'total_payout' => $fytdCurrent,
                    'fortnightly' => $fortnightlyCurrent,
                ],
                'fy_previous' => [
                    'label' => ($fyPrevious - 1).'/'.substr((string) $fyPrevious, 2),
                    'period' => $fyStartPrevious->format('j M Y').' - '.$fyEndPrevious->format('j M Y'),
                    'total_payout' => $fytdPrevious,
                    'fortnightly' => $fortnightlyPrevious,
                ],
            ],
        ]);
    }

    private function financialYear(Carbon $date): int
    {
        return $date->month >= 7 ? $date->year + 1 : $date->year;
    }

    private function fortnightlyReports(int $instructorId, Carbon $start, Carbon $end): array
    {
        $bookings = Booking::where('instructor_id', $instructorId)
            ->where('status', Booking::STATUS_COMPLETED)
            ->whereBetween('scheduled_at', [$start, $end])
            ->get();

        $byFortnight = [];
        foreach ($bookings as $b) {
            $s = $b->scheduled_at->copy()->startOfDay();
            $week = (int) floor($s->diffInDays($start) / 14);
            $key = $start->copy()->addWeeks(2 * $week)->format('Y-m-d');
            if (! isset($byFortnight[$key])) {
                $byFortnight[$key] = ['date' => $start->copy()->addWeeks(2 * $week)->format('j M Y'), 'amount' => 0, 'id' => 5000000 + $week];
            }
            $byFortnight[$key]['amount'] += (float) $b->amount;
        }
        ksort($byFortnight);

        return array_values(array_map(fn ($v) => [
            'transaction_id' => '#'.$v['id'],
            'date' => $v['date'],
            'payout' => round($v['amount'], 2),
        ], $byFortnight));
    }

    private function emptyReport(): array
    {
        $now = Carbon::now();
        $fy = $now->month >= 7 ? $now->year + 1 : $now->year;
        return [
            'summary' => [
                'earnings_display' => '$0.00',
                'next_payout_amount' => 0,
                'next_payout_date' => $now->format('j M'),
                'cancellation_rate' => 0,
                'booking_hours_per_learner' => 0,
                'learner_rating' => 0,
                'earnings_by_month' => [],
                'next_payout' => 0,
                'previous_payout' => 0,
                'fytd_payout' => 0,
                'fytd_fy' => ($fy - 1).'-'.substr((string) $fy, 2),
                'all_time_earnings' => 0,
                'ave_weekly_earnings_90' => 0,
                'ave_earnings_per_hour_90' => 0,
                'upcoming_bookings' => 0,
                'credits_held' => 0,
                'searches_in_area' => 0,
                'test_packages' => 0,
                'total_booking_hrs' => 0,
                'learners_count' => 0,
                'upcoming_hrs_booked' => 0,
                'lesson_hrs_completed' => 0,
            ],
            'pending_payout' => ['next_date' => $now->format('j M Y'), 'rows' => []],
            'fy_current' => ['label' => ($fy - 1).'/'.substr((string) $fy, 2), 'period' => '', 'total_payout' => 0, 'fortnightly' => []],
            'fy_previous' => ['label' => ($fy - 2).'/'.substr((string) ($fy - 1), 2), 'period' => '', 'total_payout' => 0, 'fortnightly' => []],
        ];
    }
}

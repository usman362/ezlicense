<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\LearnerWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard data for learner: my instructor, wallet summary, upcoming bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user->isLearner()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $now = now();

        // My instructor: from most recent booking (any) that has an instructor
        $lastBookingWithInstructor = Booking::where('learner_id', $user->id)
            ->whereNotNull('instructor_id')
            ->with(['instructor:id,name,phone', 'instructor.instructorProfile:id,user_id,lesson_price,lesson_duration_minutes,test_package_price,transmission,offers_test_package,vehicle_make,vehicle_model,vehicle_year,vehicle_safety_rating'])
            ->orderBy('scheduled_at', 'desc')
            ->first();

        $myInstructor = null;
        // Primary source: most recent booking with an instructor
        if ($lastBookingWithInstructor && $lastBookingWithInstructor->instructor) {
            $instructorUser = $lastBookingWithInstructor->instructor;
            $profile = $instructorUser->instructorProfile;
            $rate = $profile && $profile->lesson_price !== null
                ? '$' . number_format((float) $profile->lesson_price, 0) . '/hr'
                : null;
            $vehicle = null;
            if ($profile && ($profile->vehicle_make || $profile->vehicle_model || $profile->vehicle_year)) {
                $vehicle = trim(implode(' ', array_filter([
                    $profile->vehicle_make,
                    $profile->vehicle_model,
                    $profile->vehicle_year,
                ])));
            }
            $lessonPrice = $profile && $profile->lesson_price !== null ? (float) $profile->lesson_price : null;
            $lessonDuration = $profile ? (int) ($profile->lesson_duration_minutes ?? 60) : 60;
            $testPackagePrice = $profile && $profile->test_package_price !== null ? (float) $profile->test_package_price : 225;
            $transmission = $profile && $profile->transmission ? ucfirst(strtolower($profile->transmission)) : 'Auto';
            $myInstructor = [
                'id' => $instructorUser->id,
                'instructor_profile_id' => $profile?->id,
                'name' => $instructorUser->name,
                'phone' => $instructorUser->phone,
                'rate' => $rate,
                'vehicle' => $vehicle,
                'vehicle_safety_rating' => $profile?->vehicle_safety_rating,
                'dual_controls' => true,
                'lesson_price' => $lessonPrice,
                'lesson_duration_minutes' => $lessonDuration,
                'test_package_price' => $testPackagePrice,
                'offers_test_package' => $profile ? (bool) $profile->offers_test_package : false,
                'transmission' => $transmission,
            ];
        } else {
            // Fallback for testing / first-time learners: use any active instructor profile
            $profile = InstructorProfile::with('user')
                ->where('is_active', true)
                ->first();
            if ($profile && $profile->user) {
                $instructorUser = $profile->user;
                $rate = $profile->lesson_price !== null
                    ? '$' . number_format((float) $profile->lesson_price, 0) . '/hr'
                    : null;
                $vehicle = null;
                if ($profile->vehicle_make || $profile->vehicle_model || $profile->vehicle_year) {
                    $vehicle = trim(implode(' ', array_filter([
                        $profile->vehicle_make,
                        $profile->vehicle_model,
                        $profile->vehicle_year,
                    ])));
                }
                $lessonPrice = $profile->lesson_price !== null ? (float) $profile->lesson_price : null;
                $lessonDuration = (int) ($profile->lesson_duration_minutes ?? 60);
                $testPackagePrice = $profile->test_package_price !== null ? (float) $profile->test_package_price : 225;
                $transmission = $profile->transmission ? ucfirst(strtolower($profile->transmission)) : 'Auto';
                $myInstructor = [
                    'id' => $instructorUser->id,
                    'instructor_profile_id' => $profile->id,
                    'name' => $instructorUser->name,
                    'phone' => $instructorUser->phone,
                    'rate' => $rate,
                    'vehicle' => $vehicle,
                    'vehicle_safety_rating' => $profile->vehicle_safety_rating,
                    'dual_controls' => true,
                    'lesson_price' => $lessonPrice,
                    'lesson_duration_minutes' => $lessonDuration,
                    'test_package_price' => $testPackagePrice,
                    'offers_test_package' => (bool) $profile->offers_test_package,
                    'transmission' => $transmission,
                ];
            }
        }

        // Wallet
        $wallet = LearnerWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'non_refundable_credit' => 0]
        );
        $walletSummary = [
            'balance' => (float) $wallet->balance,
            'balance_display' => '$' . number_format((float) $wallet->balance, 2),
            'non_refundable_credit_display' => '$' . number_format((float) $wallet->non_refundable_credit, 2),
        ];

        // Upcoming bookings (confirmed or proposed, future)
        $upcomingBookings = Booking::where('learner_id', $user->id)
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PROPOSED])
            ->where('scheduled_at', '>', $now)
            ->with(['instructor:id,name', 'suburb.state'])
            ->orderBy('scheduled_at', 'asc')
            ->limit(20)
            ->get()
            ->map(function (Booking $b) {
                $location = $b->suburb
                    ? trim(implode(' ', array_filter([$b->suburb->name, $b->suburb->postcode, $b->suburb->state?->code])))
                    : null;
                return [
                    'id' => $b->id,
                    'scheduled_at' => $b->scheduled_at->toIso8601String(),
                    'duration_minutes' => (int) $b->duration_minutes,
                    'type' => $b->type,
                    'transmission' => $b->transmission,
                    'instructor_name' => $b->instructor?->name,
                    'location' => $location,
                ];
            });

        // KPI stats
        $completedCount = Booking::where('learner_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();
        $upcomingCount = Booking::where('learner_id', $user->id)
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PROPOSED])
            ->where('scheduled_at', '>', $now)
            ->count();
        $totalMinutes = (int) Booking::where('learner_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->sum('duration_minutes');
        $totalHours = round($totalMinutes / 60, 1);

        $stats = [
            'upcoming_count'  => $upcomingCount,
            'completed_count' => $completedCount,
            'total_hours'     => $totalHours,
        ];

        return response()->json([
            'data' => [
                'my_instructor'     => $myInstructor,
                'wallet'            => $walletSummary,
                'upcoming_bookings' => $upcomingBookings,
                'stats'             => $stats,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InstructorProfile;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $stats = [
            'users_count'       => User::count(),
            'learners_count'    => User::where('role', User::ROLE_LEARNER)->count(),
            'instructors_count' => InstructorProfile::count(),
            'bookings_count'    => Booking::count(),

            'bookings_this_month'    => Booking::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),
            'revenue_this_month'     => Booking::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)
                                            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                                            ->sum('amount'),
            'new_users_this_month'   => User::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),

            'pending_bookings'   => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'confirmed_bookings' => Booking::where('status', Booking::STATUS_CONFIRMED)->count(),
            'completed_bookings' => Booking::where('status', Booking::STATUS_COMPLETED)->count(),
            'cancelled_bookings' => Booking::where('status', Booking::STATUS_CANCELLED)->count(),

            'pending_verification' => InstructorProfile::where('verification_status', 'pending')->count(),
            'verified_instructors' => InstructorProfile::where('verification_status', 'verified')->count(),
            'inactive_users' => User::where('is_active', false)->count(),
        ];

        $recentBookings = Booking::with(['learner', 'instructor'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentUsers = User::orderByDesc('created_at')->limit(10)->get();

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $chartData[] = [
                'label' => $month->format('M Y'),
                'count' => Booking::whereMonth('created_at', $month->month)
                               ->whereYear('created_at', $month->year)
                               ->count(),
                'revenue' => (float) Booking::whereMonth('created_at', $month->month)
                               ->whereYear('created_at', $month->year)
                               ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                               ->sum('amount'),
            ];
        }

        return view('admin.dashboard', [
            'stats'          => $stats,
            'recentBookings' => $recentBookings,
            'recentUsers'    => $recentUsers,
            'chartData'      => $chartData,
        ]);
    }
}

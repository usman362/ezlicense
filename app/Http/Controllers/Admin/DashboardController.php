<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\InstructorProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'learners_count' => User::where('role', User::ROLE_LEARNER)->count(),
            'instructors_count' => InstructorProfile::count(),
            'bookings_count' => Booking::count(),
        ];

        return view('admin.dashboard', ['stats' => $stats]);
    }
}

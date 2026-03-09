<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['learner', 'instructor'])->orderByDesc('scheduled_at')->paginate(50);
        return view('admin.bookings.index', ['bookings' => $bookings]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['learner', 'instructor', 'suburb']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('learner', fn($lq) => $lq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('instructor', fn($iq) => $iq->where('name', 'like', "%{$search}%"))
                  ->orWhere('id', $search);
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $bookings = $query->orderByDesc('scheduled_at')->paginate(30)->withQueryString();

        return view('admin.bookings.index', ['bookings' => $bookings]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,proposed,confirmed,completed,cancelled,no_show',
        ]);

        $booking->status = $request->input('status');
        if ($booking->status === 'cancelled') {
            $booking->cancelled_at = now();
            $booking->cancellation_reason = $request->input('reason', 'Cancelled by admin');
        }
        $booking->save();

        return redirect()->back()->with('message', "Booking #{$booking->id} status updated to " . ucfirst($booking->status) . ".");
    }
}

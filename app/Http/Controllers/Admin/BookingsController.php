<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\ReviewRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $previousStatus = $booking->status;
        $booking->status = $request->input('status');

        if ($booking->status === 'cancelled') {
            $booking->cancelled_at = now();
            $booking->cancellation_reason = $request->input('reason', 'Cancelled by admin');
        }
        $booking->save();

        // If newly marked as completed, send review request to learner
        if ($previousStatus !== Booking::STATUS_COMPLETED && $booking->status === Booking::STATUS_COMPLETED) {
            try {
                $learner = User::find($booking->learner_id);
                if ($learner) {
                    $booking->load('instructor');
                    $learner->notify(new ReviewRequested($booking));
                }
            } catch (\Throwable $e) {
                Log::warning('Review request notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('message', "Booking #{$booking->id} status updated to " . ucfirst($booking->status) . ".");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\LessonConfirmationRequest;
use App\Notifications\ReviewRequested;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
            'status' => 'required|in:pending,proposed,confirmed,instructor_arrived,in_progress,completed,cancelled,no_show',
        ]);

        $previousStatus = $booking->status;
        $booking->status = $request->input('status');

        if ($booking->status === 'cancelled') {
            $booking->cancelled_at = now();
            $booking->cancellation_reason = $request->input('reason', 'Cancelled by admin');
        }
        $booking->save();

        // If newly marked as completed, send confirmation request + review request to learner
        if ($previousStatus !== Booking::STATUS_COMPLETED && $booking->status === Booking::STATUS_COMPLETED) {
            try {
                $learner = User::find($booking->learner_id);
                if ($learner) {
                    $booking->load('instructor');
                    $booking->generateConfirmationToken();
                    $learner->notify(new LessonConfirmationRequest($booking));
                    $learner->notify(new ReviewRequested($booking));
                }
            } catch (\Throwable $e) {
                Log::warning('Lesson confirmation/review notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('message', "Booking #{$booking->id} status updated to " . ucfirst($booking->status) . ".");
    }

    /**
     * Return ALL bookings (across all instructors) for the admin calendar view.
     * GET /api/admin/calendar/bookings?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
    public function calendarBookings(Request $request): JsonResponse
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->subDays(60)->startOfDay();
        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->addDays(60)->endOfDay();

        $bookings = Booking::with(['learner:id,name,email,phone', 'instructor:id,name,email,phone', 'suburb.state'])
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->map(fn (Booking $b) => [
                'id' => $b->id,
                'learner' => $b->learner ? ['id' => $b->learner->id, 'name' => $b->learner->name, 'email' => $b->learner->email, 'phone' => $b->learner->phone] : null,
                'instructor' => $b->instructor ? ['id' => $b->instructor->id, 'name' => $b->instructor->name] : null,
                'suburb' => $b->suburb ? [
                    'id' => $b->suburb->id,
                    'name' => $b->suburb->name,
                    'postcode' => $b->suburb->postcode,
                    'state_code' => $b->suburb->state?->code,
                    'location' => implode(' ', array_filter([$b->suburb->name, $b->suburb->postcode, $b->suburb->state?->code])),
                ] : null,
                'type' => $b->type,
                'transmission' => $b->transmission,
                'scheduled_at' => $b->scheduled_at->toIso8601String(),
                'duration_minutes' => $b->duration_minutes,
                'amount' => (float) $b->amount,
                'status' => $b->status,
                'payment_status' => $b->payment_status ?? ($b->status === Booking::STATUS_COMPLETED ? 'paid' : ($b->status === Booking::STATUS_CANCELLED ? 'refunded' : 'pending')),
                'learner_notes' => $b->learner_notes,
                'cancellation_reason' => $b->cancellation_reason,
            ]);

        return response()->json(['data' => $bookings]);
    }
}

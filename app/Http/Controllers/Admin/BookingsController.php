<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LearnerTransaction;
use App\Models\LearnerWallet;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Notifications\LessonConfirmationRequest;
use App\Notifications\RefundProcessed;
use App\Notifications\ReviewRequested;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['learner', 'instructor', 'suburb', 'instructorProfile']);

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
     * Issue a refund on a booking. Admin chooses method:
     *   - wallet           → instant credit to learner's Secure Licence wallet
     *   - original_payment → manual: card refund processed externally (Stripe/etc.); just records the reference
     *   - manual_bank      → manual: bank transfer outside platform; just records the reference
     *
     * Always:
     *   - Updates booking refund_* fields + sets payment_status='refunded' (or 'partially_refunded' if partial)
     *   - Sends RefundProcessed email to learner with full receipt
     *   - Optionally auto-cancels the booking (admin checkbox)
     */
    public function refund(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:' . max(0.01, (float) $booking->amount)],
            'method'      => ['required', 'in:wallet,original_payment,manual_bank'],
            'reason'      => ['required', 'string', 'max:500'],
            'reference'   => ['nullable', 'string', 'max:100'],
            'also_cancel' => ['nullable', 'boolean'],
        ]);

        // Already fully refunded? Block double-processing.
        if ($booking->payment_status === Booking::PAYMENT_REFUNDED && (float) $booking->refund_amount >= (float) $booking->amount) {
            return back()->withErrors(['amount' => 'This booking is already fully refunded.']);
        }

        $amount    = round((float) $data['amount'], 2);
        $isFull    = $amount >= (float) $booking->amount;
        $method    = $data['method'];
        $alsoCancel= ! empty($data['also_cancel']);

        try {
            DB::transaction(function () use ($booking, $amount, $method, $data, $alsoCancel, $isFull) {
                // 1) Wallet credit (if applicable)
                if ($method === 'wallet') {
                    $wallet = LearnerWallet::firstOrCreate(
                        ['user_id' => $booking->learner_id],
                        ['balance' => 0, 'non_refundable_credit' => 0]
                    );
                    $wallet->balance = (float) $wallet->balance + $amount;
                    $wallet->save();

                    LearnerTransaction::create([
                        'user_id'       => $booking->learner_id,
                        'type'          => LearnerTransaction::TYPE_REFUND,
                        'description'   => "Refund for booking #{$booking->id}: " . ($data['reason'] ?: 'no reason given'),
                        'amount'        => $amount, // positive — credit
                        'balance_after' => $wallet->balance,
                        'booking_id'    => $booking->id,
                    ]);
                }

                // 2) Update the booking
                $booking->update([
                    'refund_amount'         => $amount,
                    'refund_method'         => $method,
                    'refund_reason'         => $data['reason'],
                    'refund_reference'      => $data['reference'] ?? null,
                    'refunded_at'           => now(),
                    'refunded_by_user_id'   => Auth::id(),
                    'payment_status'        => $isFull ? Booking::PAYMENT_REFUNDED : ($booking->payment_status ?: Booking::PAYMENT_REFUNDED),
                ]);

                // 3) Auto-cancel the booking if admin asked
                if ($alsoCancel && $booking->status !== Booking::STATUS_CANCELLED) {
                    $booking->update([
                        'status'              => Booking::STATUS_CANCELLED,
                        'cancelled_at'        => now(),
                        'cancelled_by_id'     => Auth::id(),
                        'cancellation_reason' => 'Cancelled by admin (refund issued)',
                        'cancellation_message'=> $data['reason'],
                    ]);
                }
            });

            // 4) Email the learner the refund receipt (silent failure — refund still saved)
            try {
                $learner = User::find($booking->learner_id);
                if ($learner) {
                    $learner->notify(new RefundProcessed($booking->fresh()));
                    // If we also cancelled, send the upgraded BookingCancelled email which
                    // now includes the refund breakdown
                    if ($alsoCancel) {
                        $learner->notify(new BookingCancelled($booking->fresh(), $data['reason'], 'Refund of $' . number_format($amount, 2) . ' has been processed.'));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Refund email failed for booking {$booking->id}: " . $e->getMessage());
            }

            $msg = "Refund of \${$amount} processed for booking #{$booking->id}";
            $msg .= $alsoCancel ? ' (and booking cancelled). ' : '. ';
            $msg .= 'Learner has been emailed a receipt.';

            return back()->with('message', $msg);
        } catch (\Throwable $e) {
            Log::error("Refund failed for booking {$booking->id}: " . $e->getMessage());
            return back()->withErrors(['amount' => 'Something went wrong processing the refund: ' . $e->getMessage()]);
        }
    }

    /**
     * Manually hold a completed booking's payment so it won't be paid out.
     * Used when admin wants to investigate a dispute / fraud signal / no-show claim.
     */
    public function holdPayment(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        if ($booking->payment_held_at) {
            return back()->withErrors(['hold' => 'Payment is already on hold.']);
        }

        $booking->update([
            'payment_held_at'         => now(),
            'payment_hold_reason'     => $data['reason'],
            'payment_held_by_user_id' => Auth::id(),
            // Clawback any prior release so the next payout run skips this booking
            'payment_released_at'     => null,
        ]);

        return back()->with('message', "Payment for booking #{$booking->id} is now on hold. It won't be included in payouts until released.");
    }

    /**
     * Release a held payment (or release a pending payment immediately, bypassing the 24h window).
     */
    public function releasePayment(Request $request, Booking $booking)
    {
        if (! $booking->payment_held_at && $booking->payment_released_at) {
            return back()->withErrors(['release' => 'Payment is already released.']);
        }

        $booking->update([
            'payment_held_at'      => null,
            'payment_hold_reason'  => null,
            'payment_released_at'  => now(),
        ]);

        return back()->with('message', "Payment for booking #{$booking->id} has been released. It will be included in the next payout run.");
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

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Notifications\BookingConfirmed;
use App\Notifications\PaymentReceipt;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Stripe Checkout flow:
 *   GET  /pay/{booking}           → redirect to Stripe Checkout session
 *   GET  /pay/success/{booking}   → user redirected back from Stripe on success
 *   GET  /pay/cancel/{booking}    → user clicked back / cancelled
 *   POST /stripe/webhook          → Stripe → us (server-to-server, async)
 *
 * The webhook is the source of truth for marking a booking as paid.
 * The success_url just shows a friendly "thanks" screen — never trust
 * the redirect alone (it can be spoofed by the user).
 */
class PaymentController extends Controller
{
    public function __construct(protected StripeService $stripe) {}

    /**
     * Send the learner to Stripe's hosted checkout page.
     * Only the booking's learner (or an admin) can initiate payment.
     */
    public function checkout(Booking $booking)
    {
        $user = Auth::user();
        abort_unless($user, 401);
        abort_unless($user->id === $booking->learner_id || $user->isAdmin(), 403);

        // Already paid? Send straight to the success page.
        if ($booking->payment_status === Booking::PAYMENT_PAID) {
            return redirect()->route('stripe.success', ['booking' => $booking->id]);
        }

        try {
            $url = $this->stripe->createCheckoutSession($booking);
            return redirect()->away($url);
        } catch (\Throwable $e) {
            Log::error('Stripe checkout creation failed for booking #' . $booking->id . ': ' . $e->getMessage());
            return redirect()->route('learner.dashboard')
                ->withErrors(['payment' => 'Could not start payment. Please try again or contact support.']);
        }
    }

    /**
     * Stripe redirects here after successful payment.
     * We verify with Stripe before declaring success — but the actual
     * payment_status flip happens in the webhook.
     */
    public function success(Request $request, Booking $booking)
    {
        $sessionId = $request->query('session_id');

        // Optional verification — handles ALL bookings in the session (not just the one
        // in the URL). Useful when webhook is not yet configured.
        if ($sessionId) {
            try {
                $session = $this->stripe->getCheckoutSession($sessionId);
                if ($session->payment_status === 'paid') {
                    $bookingIdsCsv = $session->metadata->booking_ids ?? null;
                    $ids = $bookingIdsCsv
                        ? array_filter(array_map('intval', explode(',', $bookingIdsCsv)))
                        : [(int) $booking->id];

                    foreach (Booking::whereIn('id', $ids)->get() as $b) {
                        if ($b->payment_status !== Booking::PAYMENT_PAID) {
                            $this->markBookingPaid($b, $session);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Stripe session retrieval failed: ' . $e->getMessage());
            }
        }

        return view('payment.success', [
            'booking' => $booking->fresh(),
        ]);
    }

    /**
     * User cancelled the payment / clicked back. Booking stays pending.
     * Learner can resume from their dashboard later.
     */
    public function cancel(Booking $booking)
    {
        return view('payment.cancel', [
            'booking' => $booking->fresh(),
        ]);
    }

    /**
     * Stripe → us webhook. Source of truth.
     *
     * Events we care about:
     *   checkout.session.completed → mark booking paid + send receipts
     *   charge.refunded            → refund acknowledged (if issued via dashboard)
     *   payment_intent.payment_failed → mark payment_status = failed
     */
    public function webhook(Request $request)
    {
        try {
            $event = $this->stripe->verifyWebhook(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
            );
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature invalid: ' . $e->getMessage());
            return response()->json(['error' => 'invalid_signature'], 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleChargeRefunded($event->data->object);
                    break;

                default:
                    // Ignore — Stripe sends many events we don't need.
                    Log::info('Stripe webhook ignored: ' . $event->type);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook handler error (' . $event->type . '): ' . $e->getMessage());
            // Return 200 anyway so Stripe doesn't keep retrying — we've logged it.
        }

        return response()->json(['received' => true]);
    }

    /* ─────────────── Webhook handlers ─────────────── */

    private function handleCheckoutCompleted($session): void
    {
        // Multi-booking session has booking_ids metadata (comma-separated).
        // Single-booking session has booking_id metadata + client_reference_id.
        $bookingIdsCsv = $session->metadata->booking_ids ?? null;
        $bookingIds = $bookingIdsCsv
            ? array_filter(array_map('intval', explode(',', $bookingIdsCsv)))
            : [];

        // Fallback to single-booking lookup
        if (empty($bookingIds)) {
            $single = $session->client_reference_id ?? ($session->metadata->booking_id ?? null);
            if ($single) $bookingIds[] = (int) $single;
        }

        if (empty($bookingIds)) {
            Log::warning('Stripe checkout completed without resolvable booking ids');
            return;
        }

        $bookings = Booking::whereIn('id', $bookingIds)->get();
        if ($bookings->isEmpty()) {
            Log::warning('Stripe checkout completed for unknown booking ids: ' . implode(',', $bookingIds));
            return;
        }

        foreach ($bookings as $booking) {
            // Idempotent — already paid, skip
            if ($booking->payment_status === Booking::PAYMENT_PAID) continue;
            $this->markBookingPaid($booking, $session);
        }
    }

    private function handlePaymentFailed($paymentIntent): void
    {
        $bookingId = $paymentIntent->metadata->booking_id ?? null;
        if (! $bookingId) return;

        $booking = Booking::find($bookingId);
        if (! $booking) return;

        $booking->update([
            'payment_status' => 'failed',
        ]);

        Log::info("Booking #{$booking->id} payment failed via Stripe webhook.");
    }

    private function handleChargeRefunded($charge): void
    {
        // Only useful when refund was issued OUTSIDE our admin panel
        // (e.g. directly via Stripe dashboard). Our admin flow already
        // records the refund — this is a safety net.
        $paymentIntentId = $charge->payment_intent ?? null;
        if (! $paymentIntentId) return;

        $booking = Booking::where('stripe_payment_intent_id', $paymentIntentId)->first();
        if (! $booking || $booking->refunded_at) return;

        Log::info("Stripe dashboard refund detected for booking #{$booking->id} — recording.");

        // Note: charge->amount_refunded is in cents
        $refunded = ((float) $charge->amount_refunded) / 100;
        $booking->update([
            'refund_amount'    => $refunded,
            'refund_method'    => 'original_payment',
            'refund_reason'    => 'Refunded from Stripe dashboard',
            'refunded_at'      => now(),
            'payment_status'   => $refunded >= (float) $booking->amount
                ? Booking::PAYMENT_REFUNDED
                : $booking->payment_status,
        ]);
    }

    /* ─────────────── Helpers ─────────────── */

    /**
     * Mark a booking as paid and trigger downstream notifications.
     */
    private function markBookingPaid(Booking $booking, $session): void
    {
        $paymentIntentId = is_string($session->payment_intent)
            ? $session->payment_intent
            : ($session->payment_intent->id ?? null);

        $chargeId = null;
        if (is_object($session->payment_intent ?? null)) {
            $chargeId = $session->payment_intent->latest_charge ?? null;
            if (is_object($chargeId)) {
                $chargeId = $chargeId->id ?? null;
            }
        }

        $booking->update([
            'status'                   => Booking::STATUS_CONFIRMED, // flip from PROPOSED → CONFIRMED
            'payment_status'           => Booking::PAYMENT_PAID,
            'payment_method'           => 'card',
            'stripe_payment_intent_id' => $paymentIntentId,
            'stripe_charge_id'         => $chargeId,
        ]);

        Log::info("Booking #{$booking->id} marked as paid via Stripe.");

        // Send confirmation + receipt — silent failure (don't block webhook)
        try {
            $fresh = $booking->fresh();
            $recipient = $booking->learner ?: null;

            if ($recipient) {
                $recipient->notify(new BookingConfirmed($fresh));
                $recipient->notify(new PaymentReceipt(
                    bookings: [$fresh],
                    totalCharged: (float) $booking->amount,
                    paymentMethod: 'card',
                    transactionRef: $paymentIntentId,
                ));
            } elseif ($booking->guest_email) {
                \Illuminate\Support\Facades\Notification::route('mail', $booking->guest_email)
                    ->notify(new BookingConfirmed($fresh));
            }

            // Notify instructor + admin
            $instructor = $booking->instructor_id ? \App\Models\User::find($booking->instructor_id) : null;
            if ($instructor) {
                $instructor->notify(new \App\Notifications\InstructorNewBooking($fresh));
            }
        } catch (\Throwable $e) {
            Log::warning("Booking confirmation email failed: " . $e->getMessage());
        }
    }
}

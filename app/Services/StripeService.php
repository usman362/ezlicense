<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Refund as StripeRefund;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Single source of truth for all Stripe API calls.
 *
 * Methods:
 *   - createCheckoutSession(Booking)   → returns redirect URL
 *   - refundCharge(Booking, $amount)   → calls Stripe refund API
 *   - verifyWebhook($payload, $sigHeader) → validates signed event
 */
class StripeService
{
    protected StripeClient $client;

    public function __construct()
    {
        $secret = (string) config('stripe.secret_key');
        if ($secret === '') {
            throw new \RuntimeException('STRIPE_SECRET is not configured in .env');
        }

        Stripe::setApiKey($secret);
        Stripe::setApiVersion((string) config('stripe.api_version'));

        $this->client = new StripeClient($secret);
    }

    /**
     * Create a Stripe Checkout Session for a pending booking.
     * Returns the hosted checkout URL the user should be redirected to.
     */
    public function createCheckoutSession(Booking $booking): string
    {
        $booking->loadMissing('instructor', 'learner');

        $serviceLabel = $booking->type === Booking::TYPE_TEST_PACKAGE
            ? 'Driving Test Package'
            : 'Driving Lesson';

        $description = sprintf(
            '%s with %s on %s',
            $serviceLabel,
            $booking->instructor?->name ?? 'instructor',
            $booking->scheduled_at?->format('j M Y, H:i') ?? 'TBC'
        );

        $session = $this->client->checkout->sessions->create([
            'mode'                 => 'payment',
            'payment_method_types' => ['card'],
            'client_reference_id'  => (string) $booking->id,
            'customer_email'       => $booking->learner?->email,

            'line_items' => [[
                'price_data' => [
                    'currency'     => (string) config('stripe.currency', 'aud'),
                    'unit_amount'  => (int) round(((float) $booking->amount) * 100), // cents
                    'product_data' => [
                        'name'        => $serviceLabel . ' — Booking #' . $booking->id,
                        'description' => $description,
                    ],
                ],
                'quantity' => 1,
            ]],

            'metadata' => [
                'booking_id'    => (string) $booking->id,
                'learner_id'    => (string) $booking->learner_id,
                'instructor_id' => (string) $booking->instructor_id,
            ],

            'success_url' => route('stripe.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('stripe.cancel', ['booking' => $booking->id]),

            // 30-minute checkout window
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        // Persist the session id on the booking so we can correlate webhook events
        $booking->update([
            'stripe_checkout_session_id' => $session->id,
            'payment_status'             => Booking::PAYMENT_PENDING,
        ]);

        return $session->url;
    }

    /**
     * Issue a refund through Stripe for a booking's previous charge.
     * Returns the Stripe refund object — caller updates the booking.
     */
    public function refundCharge(Booking $booking, float $amount): StripeRefund
    {
        if (empty($booking->stripe_payment_intent_id)) {
            throw new \RuntimeException(
                "Booking #{$booking->id} has no stripe_payment_intent_id — cannot refund via Stripe."
            );
        }

        $refund = $this->client->refunds->create([
            'payment_intent' => $booking->stripe_payment_intent_id,
            'amount'         => (int) round($amount * 100), // cents
            'reason'         => 'requested_by_customer',
            'metadata'       => [
                'booking_id' => (string) $booking->id,
                'issued_by'  => (string) auth()->id(),
            ],
        ]);

        return $refund;
    }

    /**
     * Retrieve full Checkout Session including expanded payment intent.
     * Useful for verifying success_url returns.
     */
    public function getCheckoutSession(string $sessionId): StripeSession
    {
        return $this->client->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent', 'payment_intent.latest_charge'],
        ]);
    }

    /**
     * Verify webhook signature and return the decoded event.
     * Throws on invalid signature.
     */
    public function verifyWebhook(string $payload, string $signatureHeader): \Stripe\Event
    {
        $secret = (string) config('stripe.webhook_secret');
        if ($secret === '') {
            throw new \RuntimeException('STRIPE_WEBHOOK_SECRET is not configured.');
        }

        return Webhook::constructEvent($payload, $signatureHeader, $secret);
    }
}

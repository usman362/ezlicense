<?php

/*
|--------------------------------------------------------------------------
| Stripe configuration
|--------------------------------------------------------------------------
| All values pulled from env. Pulled via config() (not env()) so they
| survive `php artisan config:cache` in production.
|
| Test keys start with sk_test_ / pk_test_ — use these locally + staging.
| Live keys start with sk_live_ / pk_live_ — use only in production once
| flow is verified.
*/

return [
    // Server-side secret key — used to call Stripe API from our backend.
    'secret_key' => env('STRIPE_SECRET'),

    // Client-side publishable key — sent to the browser (safe to expose).
    // Not actually used in the Checkout flow (we redirect via session URL),
    // but kept here for future Stripe.js / Elements expansion.
    'publishable_key' => env('STRIPE_KEY'),

    // Endpoint signing secret — for verifying webhook payloads.
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    // Currency code (lowercase). Australia = aud.
    'currency' => env('STRIPE_CURRENCY', 'aud'),

    // Stripe API version pin — keeps Stripe behaviour stable when they
    // release breaking changes. Override only after testing.
    'api_version' => env('STRIPE_API_VERSION', '2024-11-20.acacia'),
];

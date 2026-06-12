<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Remove obsolete payment settings — PayPal was scrapped (Stripe-only)
        // and the legacy single-key Stripe fields are superseded by the
        // mode-prefixed (stripe_test_* / stripe_live_*) ones.
        DB::table('site_settings')
            ->whereIn('key', [
                // PayPal — scrapped (Stripe-only)
                'paypal_enabled',
                'paypal_client_id',
                'paypal_client_secret',
                'paypal_mode',
                'enable_paypal',
                // Legacy single-key Stripe — superseded by test/live prefixed keys
                'stripe_publishable_key',
                'stripe_secret_key',
                'stripe_webhook_secret',
                // Stripe kill-switch — confusing, no useful purpose since Stripe
                // is the only gateway. Disabling it would break all bookings.
                'enable_stripe',
            ])
            ->delete();

        // Clear cached settings so the admin UI re-renders cleanly.
        try { \Illuminate\Support\Facades\Cache::forget('site_settings.all'); } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // No-op — these settings won't be re-created automatically; if needed
        // they will be picked up by SiteSetting::seedDefaults() based on the
        // SiteSetting model's current definition.
    }
};

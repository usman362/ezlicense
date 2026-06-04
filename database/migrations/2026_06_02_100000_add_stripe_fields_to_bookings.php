<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Stripe payment metadata — saved at the time of Checkout creation
            // and updated when the webhook confirms completion.
            $table->string('stripe_checkout_session_id', 191)->nullable()->after('payment_method')->index();
            $table->string('stripe_payment_intent_id', 191)->nullable()->after('stripe_checkout_session_id')->index();
            $table->string('stripe_charge_id', 191)->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_refund_id', 191)->nullable()->after('refund_reference');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'stripe_charge_id',
                'stripe_refund_id',
            ]);
        });
    }
};

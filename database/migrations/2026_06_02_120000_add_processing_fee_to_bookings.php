<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Waivable per-booking processing fee. $2 by default, $0 when learner
            // books a package of 5+ lessons (saves Stripe per-transaction overhead).
            // `platform_fee` is reused as the non-waivable platform service fee.
            $table->decimal('processing_fee', 8, 2)->default(0)->after('platform_fee');

            // Tracked separately for the admin Fees Dashboard so we can compute
            // platform's actual profit (service+processing fees collected minus
            // Stripe's cut).
            $table->decimal('stripe_fee_estimate', 8, 2)->default(0)->after('processing_fee');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['processing_fee', 'stripe_fee_estimate']);
        });
    }
};

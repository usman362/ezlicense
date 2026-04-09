<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('instructor_net_amount', 10, 2)->nullable()->after('platform_fee');
            $table->foreignId('instructor_payout_id')->nullable()->after('instructor_net_amount')
                ->constrained('instructor_payouts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['instructor_payout_id']);
            $table->dropColumn(['instructor_net_amount', 'instructor_payout_id']);
        });
    }
};

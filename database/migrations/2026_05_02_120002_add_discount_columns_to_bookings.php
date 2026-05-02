<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('coupon_discount_amount', 10, 2)->default(0)->after('amount');
            $table->string('coupon_code', 50)->nullable()->after('coupon_discount_amount');
            $table->decimal('referral_discount_amount', 10, 2)->default(0)->after('coupon_code');
            $table->decimal('bulk_discount_amount', 10, 2)->default(0)->after('referral_discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['coupon_discount_amount', 'coupon_code', 'referral_discount_amount', 'bulk_discount_amount']);
        });
    }
};

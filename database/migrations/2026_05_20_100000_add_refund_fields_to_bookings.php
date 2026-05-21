<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('refund_amount', 10, 2)->nullable()->after('coupon_discount_amount');
            $table->string('refund_method', 30)->nullable()->after('refund_amount'); // wallet | original_payment | manual_bank
            $table->text('refund_reason')->nullable()->after('refund_method');
            $table->string('refund_reference', 100)->nullable()->after('refund_reason'); // Stripe refund ID or bank ref
            $table->timestamp('refunded_at')->nullable()->after('refund_reference');
            $table->foreignId('refunded_by_user_id')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('refunded_by_user_id');
            $table->dropColumn(['refund_amount', 'refund_method', 'refund_reason', 'refund_reference', 'refunded_at']);
        });
    }
};

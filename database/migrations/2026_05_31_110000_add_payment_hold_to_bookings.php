<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the payment-hold workflow to bookings:
 *
 *   payment_held_at        → when admin (or system) flagged the payment for hold
 *   payment_released_at    → when payment was released to instructor's payout pool
 *   payment_hold_reason    → admin's reason for holding (free text)
 *   payment_held_by_user_id → which admin held it
 *
 * Workflow:
 *   1) Lesson completed                 → status=completed, payment_status=pending
 *   2) After 24h grace (if not held)    → payment_released_at=now()
 *                                          payment_status=paid (eligible for payout)
 *   3) Admin holds (anytime)            → payment_held_at=now(), payment_status stays pending
 *   4) Admin releases held payment      → payment_held_at=null, payment_status=paid
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('payment_held_at')->nullable()->after('refunded_by_user_id');
            $table->timestamp('payment_released_at')->nullable()->after('payment_held_at');
            $table->string('payment_hold_reason', 500)->nullable()->after('payment_released_at');
            $table->foreignId('payment_held_by_user_id')->nullable()->after('payment_hold_reason')->constrained('users')->nullOnDelete();

            $table->index(['payment_status', 'payment_released_at']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'payment_released_at']);
            $table->dropConstrainedForeignId('payment_held_by_user_id');
            $table->dropColumn(['payment_held_at', 'payment_released_at', 'payment_hold_reason']);
        });
    }
};

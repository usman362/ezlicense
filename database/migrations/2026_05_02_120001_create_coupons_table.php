<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coupons / promo codes.
     * Admins create codes that learners can redeem at checkout.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // user-facing redemption code (uppercase, e.g. "WELCOME10")
            $table->string('description')->nullable();

            // Discount type: 'percent' or 'fixed'
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('amount', 10, 2); // percent (e.g. 10.00 = 10%) or fixed dollars

            // Constraints
            $table->decimal('min_order_amount', 10, 2)->default(0); // minimum cart total to redeem
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // cap on percent-type savings (null = no cap)
            $table->unsignedInteger('max_uses')->nullable(); // total redemptions allowed (null = unlimited)
            $table->unsignedInteger('max_uses_per_user')->default(1); // per-user cap
            $table->unsignedInteger('used_count')->default(0); // running counter

            // Validity window
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Optional restriction: first booking only
            $table->boolean('first_booking_only')->default(false);

            $table->timestamps();
            $table->index(['is_active', 'expires_at']);
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->decimal('discount_applied', 10, 2); // actual $ discounted
            $table->decimal('order_total', 10, 2); // cart total at redemption (audit trail)
            $table->timestamps();
            $table->index(['coupon_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupons');
    }
};

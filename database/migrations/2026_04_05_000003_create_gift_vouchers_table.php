<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique();
            $table->unsignedBigInteger('purchaser_id')->nullable();
            $table->unsignedBigInteger('redeemer_id')->nullable();
            $table->string('purchaser_name')->nullable();
            $table->string('purchaser_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('personal_message', 500)->nullable();
            $table->decimal('amount', 8, 2);
            $table->decimal('remaining_amount', 8, 2);
            $table->string('voucher_type')->default('custom'); // 1hour, 5hour, custom
            $table->string('status')->default('pending'); // pending, paid, active, redeemed, partially_redeemed, expired, cancelled
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('purchaser_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('redeemer_id')->references('id')->on('users')->nullOnDelete();
            $table->index('code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_vouchers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('service_fee', 10, 2)->default(5.00);
            $table->decimal('processing_fee', 10, 2)->default(2.00);
            $table->decimal('gst_on_fees', 8, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_payout_items');
    }
};

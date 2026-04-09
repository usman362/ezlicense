<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 30)->unique();

            // Period (weekly: Sunday 00:00 → Saturday 23:59 AEST)
            $table->dateTime('period_start');
            $table->dateTime('period_end');

            // Amounts
            $table->unsignedInteger('bookings_count')->default(0);
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('service_fee_total', 12, 2)->default(0);
            $table->decimal('processing_fee_total', 12, 2)->default(0);
            $table->decimal('gst_on_fees', 10, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->decimal('adjustment_amount', 10, 2)->default(0);

            // Status lifecycle
            $table->enum('status', ['pending', 'approved', 'processing', 'paid', 'failed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference', 255)->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->unique(['instructor_profile_id', 'period_start', 'period_end'], 'ip_profile_period_unique');
            $table->index('status');
            $table->index('period_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_payouts');
    }
};

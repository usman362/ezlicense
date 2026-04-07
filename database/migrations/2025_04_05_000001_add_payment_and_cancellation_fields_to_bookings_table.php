<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Payment fields (fixes critical bug — processPayment was setting these without columns)
            $table->foreignId('instructor_profile_id')->nullable()->after('instructor_id')
                ->constrained('instructor_profiles')->nullOnDelete();
            $table->decimal('platform_fee', 10, 2)->default(0)->after('amount');
            $table->string('payment_method')->nullable()->after('platform_fee'); // card, paypal, wallet
            $table->string('payment_status')->default('pending')->after('payment_method'); // pending, paid, refunded

            // Enhanced cancellation fields (matching live site flow)
            $table->string('cancellation_reason_code')->nullable()->after('cancellation_reason');
            // illness_family_emergency, double_booked, car_trouble, weather_conditions, requested_by_learner, other
            $table->text('cancellation_message')->nullable()->after('cancellation_reason_code');
            // Message shared with the other party
            $table->foreignId('cancelled_by_id')->nullable()->after('cancelled_at')
                ->constrained('users')->nullOnDelete();
            $table->boolean('cancellation_policy_accepted')->default(false)->after('cancelled_by_id');

            // Reschedule tracking
            $table->foreignId('rescheduled_from_booking_id')->nullable()->after('cancellation_policy_accepted')
                ->constrained('bookings')->nullOnDelete();
            // Links to original booking when this is a reschedule-proposed booking
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['instructor_profile_id']);
            $table->dropForeign(['cancelled_by_id']);
            $table->dropForeign(['rescheduled_from_booking_id']);
            $table->dropColumn([
                'instructor_profile_id',
                'platform_fee',
                'payment_method',
                'payment_status',
                'cancellation_reason_code',
                'cancellation_message',
                'cancelled_by_id',
                'cancellation_policy_accepted',
                'rescheduled_from_booking_id',
            ]);
        });
    }
};

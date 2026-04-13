<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('confirmation_token', 64)->nullable()->unique()->after('rescheduled_from_booking_id');
            $table->timestamp('confirmation_sent_at')->nullable()->after('confirmation_token');
            $table->timestamp('learner_confirmed_at')->nullable()->after('confirmation_sent_at');
            $table->string('learner_confirmed_ip', 45)->nullable()->after('learner_confirmed_at');
            $table->string('learner_confirmed_user_agent', 500)->nullable()->after('learner_confirmed_ip');
            $table->timestamp('confirmation_reminded_at')->nullable()->after('learner_confirmed_user_agent');
            $table->tinyInteger('confirmation_reminder_count')->default(0)->after('confirmation_reminded_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'confirmation_token',
                'confirmation_sent_at',
                'learner_confirmed_at',
                'learner_confirmed_ip',
                'learner_confirmed_user_agent',
                'confirmation_reminded_at',
                'confirmation_reminder_count',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('instructor_arrived_at')->nullable()->after('status');
            $table->timestamp('lesson_started_at')->nullable()->after('instructor_arrived_at');
            $table->timestamp('lesson_ended_at')->nullable()->after('lesson_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['instructor_arrived_at', 'lesson_started_at', 'lesson_ended_at']);
        });
    }
};

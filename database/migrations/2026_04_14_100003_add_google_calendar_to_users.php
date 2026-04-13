<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_calendar_token')->nullable()->after('calendar_token');
            $table->string('google_calendar_id')->nullable()->after('google_calendar_token');
            $table->boolean('google_calendar_sync_enabled')->default(false)->after('google_calendar_id');
            $table->timestamp('google_calendar_last_synced_at')->nullable()->after('google_calendar_sync_enabled');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->after('lesson_ended_at');
            $table->string('google_event_id_learner')->nullable()->after('google_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_token',
                'google_calendar_id',
                'google_calendar_sync_enabled',
                'google_calendar_last_synced_at',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'google_event_id_learner']);
        });
    }
};

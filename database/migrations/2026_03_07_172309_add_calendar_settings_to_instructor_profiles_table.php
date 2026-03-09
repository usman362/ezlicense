<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('travel_buffer_same_mins')->default(30)->after('notification_sms_marketing');
            $table->unsignedSmallInteger('travel_buffer_synced_mins')->default(30)->after('travel_buffer_same_mins');
            $table->unsignedSmallInteger('min_prior_notice_hours')->default(5)->after('travel_buffer_synced_mins');
            $table->unsignedSmallInteger('max_advance_notice_days')->default(75)->after('min_prior_notice_hours');
            $table->boolean('smart_scheduling_enabled')->default(true)->after('max_advance_notice_days');
            $table->unsignedTinyInteger('smart_scheduling_buffer_hrs')->default(1)->after('smart_scheduling_enabled');
            $table->boolean('attach_ics_to_emails')->default(true)->after('smart_scheduling_buffer_hrs');
            $table->string('default_calendar_view', 20)->default('day')->after('attach_ics_to_emails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'travel_buffer_same_mins', 'travel_buffer_synced_mins', 'min_prior_notice_hours',
                'max_advance_notice_days', 'smart_scheduling_enabled', 'smart_scheduling_buffer_hrs',
                'attach_ics_to_emails', 'default_calendar_view',
            ]);
        });
    }
};

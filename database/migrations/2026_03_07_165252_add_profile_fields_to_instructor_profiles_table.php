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
            $table->json('languages')->nullable()->after('bio');
            $table->boolean('association_member')->default(false)->after('languages');
            $table->unsignedTinyInteger('instructing_start_month')->nullable()->after('association_member');
            $table->unsignedSmallInteger('instructing_start_year')->nullable()->after('instructing_start_month');
            $table->boolean('service_test_existing')->default(false)->after('offers_test_package');
            $table->boolean('service_test_new')->default(false)->after('service_test_existing');
            $table->boolean('service_manual_no_vehicle')->default(false)->after('service_test_new');
            $table->boolean('notification_email_marketing')->default(true)->after('is_active');
            $table->boolean('notification_sms_marketing')->default(true)->after('notification_email_marketing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'languages', 'association_member', 'instructing_start_month', 'instructing_start_year',
                'service_test_existing', 'service_test_new', 'service_manual_no_vehicle',
                'notification_email_marketing', 'notification_sms_marketing',
            ]);
        });
    }
};

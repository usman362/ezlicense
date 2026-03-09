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
            $table->string('business_name')->nullable()->after('default_calendar_view');
            $table->string('abn', 20)->nullable()->after('business_name');
            $table->string('billing_address')->nullable()->after('abn');
            $table->boolean('gst_registered')->nullable()->after('billing_address');
            $table->string('billing_suburb')->nullable()->after('gst_registered');
            $table->string('billing_postcode', 10)->nullable()->after('billing_suburb');
            $table->string('billing_state', 10)->nullable()->after('billing_postcode');
            $table->string('payout_frequency', 30)->nullable()->after('billing_state'); // weekly, fortnightly, every_four_weeks
            $table->string('bank_account_name')->nullable()->after('payout_frequency');
            $table->string('bank_bsb', 10)->nullable()->after('bank_account_name');
            $table->string('bank_account_number', 20)->nullable()->after('bank_bsb');
            $table->timestamp('bank_details_submitted_at')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'business_name', 'abn', 'billing_address', 'gst_registered',
                'billing_suburb', 'billing_postcode', 'billing_state',
                'payout_frequency', 'bank_account_name', 'bank_bsb', 'bank_account_number',
                'bank_details_submitted_at',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SiteSetting;

return new class extends Migration
{
    public function up(): void
    {
        $fees = [
            ['group' => 'commission', 'key' => 'platform_service_fee', 'value' => '5.00', 'type' => 'number', 'label' => 'Service Fee per Booking ($)', 'hint' => 'Flat dollar amount deducted from each booking as platform service fee'],
            ['group' => 'commission', 'key' => 'payment_processing_fee', 'value' => '2.00', 'type' => 'number', 'label' => 'Processing Fee per Booking ($)', 'hint' => 'Flat dollar amount deducted for payment processing costs'],
            ['group' => 'commission', 'key' => 'minimum_payout_amount', 'value' => '1.00', 'type' => 'number', 'label' => 'Minimum Payout Amount ($)', 'hint' => 'Payouts below this amount are rolled into the next period'],
        ];

        foreach ($fees as $fee) {
            SiteSetting::firstOrCreate(
                ['key' => $fee['key']],
                $fee,
            );
        }
    }

    public function down(): void
    {
        SiteSetting::whereIn('key', [
            'platform_service_fee',
            'payment_processing_fee',
            'minimum_payout_amount',
        ])->delete();
    }
};

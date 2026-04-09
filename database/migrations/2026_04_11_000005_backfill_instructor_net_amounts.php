<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill net amounts for existing completed bookings:
        // instructor_net_amount = booking amount minus $7 flat fee ($5 service + $2 processing)
        DB::table('bookings')
            ->whereNull('instructor_net_amount')
            ->where('status', 'completed')
            ->update([
                'instructor_net_amount' => DB::raw('GREATEST(amount - 7.00, 0)'),
            ]);
    }

    public function down(): void
    {
        DB::table('bookings')->update(['instructor_net_amount' => null]);
    }
};

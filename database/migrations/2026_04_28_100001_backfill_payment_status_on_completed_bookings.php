<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill payment_status = 'paid' for any historical bookings that were completed
 * but had their payment_status left at the default 'pending' value.
 *
 * Root cause: BookingController::store() didn't set payment_status when creating
 * confirmed bookings, so the column defaulted to 'pending' and stayed there even
 * after the lesson was completed. From this migration onward, the controller
 * sets payment_status explicitly + complete()/endLesson() defensively reset it.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('bookings')
            ->where('status', 'completed')
            ->whereIn('payment_status', ['pending', 'failed'])
            ->update(['payment_status' => 'paid']);
    }

    public function down(): void
    {
        // No-op — safer to leave fixed values in place than risk reverting them
    }
};

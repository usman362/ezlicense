<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add a JSON column for the set of lesson durations an instructor offers.
 *
 * Reference (EzLicence) presents these as a multi-checkbox group:
 *   1h, 1.5h, 2h, 3h, 4h, 5h  — with 1h and 2h always required.
 *
 * We store the array of minutes (e.g. [60, 90, 120]). The existing
 * `lesson_duration_minutes` column stays as the "primary/default" duration
 * for places that still expect a single value.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->json('lesson_durations')->nullable()->after('lesson_duration_minutes');
        });

        // Backfill: every existing instructor gets [60, 120] (the two required
        // durations). If their existing single duration is something else, include it too.
        DB::table('instructor_profiles')->get(['id', 'lesson_duration_minutes'])->each(function ($row) {
            $set = [60, 120];
            $current = (int) ($row->lesson_duration_minutes ?? 0);
            if ($current > 0 && ! in_array($current, $set, true)) {
                $set[] = $current;
            }
            sort($set);
            DB::table('instructor_profiles')
                ->where('id', $row->id)
                ->update(['lesson_durations' => json_encode($set)]);
        });
    }

    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn('lesson_durations');
        });
    }
};

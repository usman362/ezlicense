<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->string('public_slug', 80)->nullable()->unique()->after('user_id');
        });

        // Backfill existing rows
        $rows = DB::table('instructor_profiles')
            ->join('users', 'instructor_profiles.user_id', '=', 'users.id')
            ->select('instructor_profiles.id', 'users.name')
            ->whereNull('instructor_profiles.public_slug')
            ->get();

        $taken = [];
        foreach ($rows as $row) {
            $base = Str::slug($row->name ?: 'instructor-' . $row->id);
            $slug = $base ?: 'instructor-' . $row->id;
            $i = 1;
            // Ensure uniqueness across DB + this run
            while (DB::table('instructor_profiles')->where('public_slug', $slug)->exists() || isset($taken[$slug])) {
                $slug = $base . '-' . ++$i;
            }
            $taken[$slug] = true;
            DB::table('instructor_profiles')->where('id', $row->id)->update(['public_slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn('public_slug');
        });
    }
};

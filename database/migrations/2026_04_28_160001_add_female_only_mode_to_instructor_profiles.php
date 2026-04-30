<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Female-instructor safety preference.
 * When `accepts_female_learners_only` is true (only meaningful for female-gendered
 * instructors), the instructor's profile is hidden from male/other learners and
 * booking is blocked unless the booker is verified female.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->boolean('accepts_female_learners_only')->default(false)->after('is_active');
            $table->index('accepts_female_learners_only');
        });
    }

    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropIndex(['accepts_female_learners_only']);
            $table->dropColumn('accepts_female_learners_only');
        });
    }
};

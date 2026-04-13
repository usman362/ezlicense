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
            $table->decimal('weighted_rating', 3, 2)->default(4.00)->after('admin_notes');
            $table->decimal('rating_points', 8, 2)->default(4.00)->after('weighted_rating');
            $table->unsignedInteger('total_completed_lessons')->default(0)->after('rating_points');
            $table->unsignedInteger('consecutive_five_stars')->default(0)->after('total_completed_lessons');
            $table->integer('recovery_deficit')->default(0)->after('consecutive_five_stars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'weighted_rating',
                'rating_points',
                'total_completed_lessons',
                'consecutive_five_stars',
                'recovery_deficit',
            ]);
        });
    }
};

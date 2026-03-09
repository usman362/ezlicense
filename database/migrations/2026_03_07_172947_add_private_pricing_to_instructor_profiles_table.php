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
            $table->decimal('lesson_price_private', 10, 2)->nullable()->after('test_package_price');
            $table->decimal('test_package_price_private', 10, 2)->nullable()->after('lesson_price_private');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn(['lesson_price_private', 'test_package_price_private']);
        });
    }
};

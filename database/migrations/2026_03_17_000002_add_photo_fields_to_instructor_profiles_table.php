<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->string('vehicle_photo')->nullable()->after('vehicle_safety_rating');
            $table->string('profile_photo')->nullable()->after('bio');
            $table->text('profile_description')->nullable()->after('profile_photo');
        });
    }

    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn(['vehicle_photo', 'profile_photo', 'profile_description']);
        });
    }
};

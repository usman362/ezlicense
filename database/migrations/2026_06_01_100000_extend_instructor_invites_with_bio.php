<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('instructor_invites', function (Blueprint $table) {
            // Pre-fill these so the instructor's profile is mostly built before they accept.
            // All nullable — admin can send a "lite" invite (just email) or a fully-loaded one.
            $table->unsignedTinyInteger('years_experience')->nullable()->after('phone');
            $table->string('transmission', 10)->nullable()->after('years_experience');    // auto | manual | both
            $table->text('bio')->nullable()->after('transmission');
            $table->foreignId('suburb_id')->nullable()->after('bio')->constrained('suburbs')->nullOnDelete();
            $table->decimal('lesson_price', 8, 2)->nullable()->after('suburb_id');
            $table->string('vehicle_make', 60)->nullable()->after('lesson_price');
            $table->string('vehicle_model', 60)->nullable()->after('vehicle_make');
            $table->unsignedSmallInteger('vehicle_year')->nullable()->after('vehicle_model');
        });
    }

    public function down(): void
    {
        Schema::table('instructor_invites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('suburb_id');
            $table->dropColumn([
                'years_experience', 'transmission', 'bio',
                'lesson_price', 'vehicle_make', 'vehicle_model', 'vehicle_year',
            ]);
        });
    }
};

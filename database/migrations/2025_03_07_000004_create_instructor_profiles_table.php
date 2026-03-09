<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('transmission')->default('both'); // auto, manual, both
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->unsignedSmallInteger('vehicle_year')->nullable();
            $table->string('vehicle_safety_rating')->nullable();
            $table->string('wwcc_number')->nullable();
            $table->timestamp('wwcc_verified_at')->nullable();
            $table->text('accreditation_details')->nullable();
            $table->decimal('lesson_price', 10, 2)->default(0);
            $table->decimal('test_package_price', 10, 2)->nullable();
            $table->unsignedSmallInteger('lesson_duration_minutes')->default(60);
            $table->boolean('offers_test_package')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};

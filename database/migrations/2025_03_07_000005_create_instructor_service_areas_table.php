<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_service_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('suburb_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['instructor_profile_id', 'suburb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_service_areas');
    }
};

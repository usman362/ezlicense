<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recurring weekly slots: e.g. Mon 9-17, Tue 9-17
        Schema::create('instructor_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->index(['instructor_profile_id', 'day_of_week']);
        });

        // Specific date overrides: blocked or extra available
        Schema::create('instructor_availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time')->nullable(); // null = whole day blocked
            $table->time('end_time')->nullable();
            $table->boolean('is_available')->default(false); // false = blocked, true = extra slot
            $table->timestamps();

            $table->index(['instructor_profile_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_availability_blocks');
        Schema::dropIfExists('instructor_availability_slots');
    }
};

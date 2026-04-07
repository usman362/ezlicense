<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_provider_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->index(['service_provider_id', 'day_of_week'], 'sp_avail_slots_provider_dow');
        });

        Schema::create('service_provider_availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_available')->default(false);
            $table->timestamps();
            $table->index(['service_provider_id', 'date'], 'sp_avail_blocks_provider_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_provider_availability_blocks');
        Schema::dropIfExists('service_provider_availability_slots');
    }
};

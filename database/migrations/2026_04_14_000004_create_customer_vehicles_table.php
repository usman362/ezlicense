<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Customer Vehicles ────────────────────────────────────
        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_make_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_model_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('colour', 30)->nullable();
            $table->string('registration', 20)->nullable();     // rego plate
            $table->string('vin', 20)->nullable();               // vehicle identification number
            $table->string('transmission', 15)->nullable();      // auto, manual
            $table->string('fuel_type', 20)->nullable();         // petrol, diesel, electric, hybrid, lpg
            $table->string('photo')->nullable();                 // uploaded photo path
            $table->text('notes')->nullable();                   // any extra info
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_vehicles');
    }
};

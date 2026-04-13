<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Which vehicle makes a mechanic works on ──────────────
        if (!Schema::hasTable('service_provider_vehicle_makes')) {
            Schema::create('service_provider_vehicle_makes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
                $table->foreignId('vehicle_make_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['service_provider_id', 'vehicle_make_id'], 'sp_vm_unique');
            });
        }

        // ── Which service types a mechanic offers ────────────────
        if (!Schema::hasTable('service_provider_mechanic_services')) {
            Schema::create('service_provider_mechanic_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
                $table->foreignId('mechanic_service_type_id')->constrained('mechanic_service_types', 'id', 'sp_ms_mst_fk')->cascadeOnDelete();
                $table->decimal('price_from', 10, 2)->nullable();
                $table->timestamps();

                $table->unique(['service_provider_id', 'mechanic_service_type_id'], 'sp_mst_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_provider_mechanic_services');
        Schema::dropIfExists('service_provider_vehicle_makes');
    }
};

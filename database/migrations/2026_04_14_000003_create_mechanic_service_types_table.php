<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Mechanic Service Types (Brakes, Servicing, Electrical, etc.) ──
        Schema::create('mechanic_service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 80)->unique();
            $table->string('icon', 40)->nullable();             // Bootstrap icon class
            $table->text('description')->nullable();
            $table->enum('category', ['servicing', 'repairs', 'electrical', 'tyres', 'inspection', 'other'])->default('repairs');
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mechanic_service_types');
    }
};

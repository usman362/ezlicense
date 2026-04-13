<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Vehicle Makes (Toyota, Honda, BMW, etc.) ─────────────
        Schema::create('vehicle_makes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('slug', 60)->unique();
            $table->string('country', 40)->nullable();          // Japan, Germany, Australia, etc.
            $table->enum('origin_type', ['japanese', 'european', 'australian', 'american', 'korean', 'chinese', 'other'])->default('other');
            $table->string('logo_url')->nullable();
            $table->boolean('is_popular')->default(false);       // Commonly seen in Australia
            $table->boolean('is_european')->default(false);      // European / luxury — harder to service
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Vehicle Models (Corolla, Civic, 3 Series, etc.) ──────
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_make_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->unsignedSmallInteger('year_from')->nullable();
            $table->unsignedSmallInteger('year_to')->nullable();
            $table->string('body_type', 30)->nullable();         // sedan, suv, ute, hatch, wagon, van
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['vehicle_make_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('vehicle_makes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->string('abn')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->json('languages')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('callout_fee', 10, 2)->default(0);
            $table->unsignedSmallInteger('default_duration_minutes')->default(60);
            $table->unsignedInteger('service_radius_km')->default(20);
            $table->string('base_suburb')->nullable();
            $table->string('base_postcode', 10)->nullable();
            $table->string('base_state', 10)->nullable();
            $table->text('service_description')->nullable();
            $table->string('license_number')->nullable();
            $table->timestamp('license_verified_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('verification_status')->default('pending'); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['service_category_id', 'is_active']);
        });

        Schema::create('service_provider_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('suburb_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['service_provider_id', 'suburb_id'], 'sp_areas_unique');
        });

        Schema::create('service_provider_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // license, insurance, id, certificate
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_provider_documents');
        Schema::dropIfExists('service_provider_areas');
        Schema::dropIfExists('service_providers');
    }
};

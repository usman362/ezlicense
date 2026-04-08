<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('category', 100)->nullable(); // e.g. conduct, safety, no_show, pricing, communication
            $table->string('subject', 255);
            $table->text('description');
            $table->text('internal_notes')->nullable();
            $table->foreignId('related_complaint_id')->nullable();
            $table->foreignId('related_booking_id')->nullable();
            $table->boolean('notified_instructor')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['instructor_profile_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_warnings');
    }
};

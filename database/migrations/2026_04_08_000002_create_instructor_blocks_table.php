<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->enum('duration_type', ['30_days', '60_days', '90_days', 'custom', 'permanent']);
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable(); // null = permanent
            $table->text('reason');
            $table->text('internal_notes')->nullable();
            $table->timestamp('lifted_at')->nullable();
            $table->foreignId('lifted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('lifted_reason')->nullable();
            $table->timestamps();

            $table->index(['instructor_profile_id', 'lifted_at']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_blocks');
    }
};

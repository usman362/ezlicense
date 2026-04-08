<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_admin_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->text('note');
            $table->boolean('pinned')->default(false);
            $table->timestamps();

            $table->index(['instructor_profile_id', 'pinned']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_admin_notes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_admin_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->text('note');
            $table->boolean('pinned')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'pinned'], 'uan_user_pinned_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_admin_notes');
    }
};

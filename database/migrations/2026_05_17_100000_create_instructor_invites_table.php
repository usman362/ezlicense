<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_invites', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('email')->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('personal_note')->nullable(); // Admin's optional message shown in the invite email
            $table->foreignId('invited_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('registered_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending | accepted | expired | cancelled
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->unsignedTinyInteger('send_count')->default(1);
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_invites');
    }
};

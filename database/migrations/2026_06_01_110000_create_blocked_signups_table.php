<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Permanent record of an email/phone that should NOT be allowed to create
        // a new account. Created when an admin blocks/bans an instructor (or learner),
        // so the same person can't re-register under a fresh invite or fresh signup.
        Schema::create('blocked_signups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email', 191)->index();
            $table->string('phone_normalized', 30)->nullable()->index(); // digits-only for fuzzy match
            $table->string('name', 150)->nullable();
            $table->string('reason', 500)->nullable();
            $table->foreignId('blocked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('blocked_at')->useCurrent();
            $table->boolean('is_active')->default(true)->index(); // admin can "unblock" by setting false
            $table->timestamps();

            $table->index(['is_active', 'email']);
        });

        // Attempt log — every time someone matching a blocked signup tries to register
        Schema::create('blocked_signup_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocked_signup_id')->constrained('blocked_signups')->cascadeOnDelete();
            $table->string('email', 191);
            $table->string('phone', 30)->nullable();
            $table->string('attempted_name', 150)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('context', 50)->nullable(); // 'invite_register' | 'signup' | 'invite_create'
            $table->timestamps();

            $table->index(['blocked_signup_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_signup_attempts');
        Schema::dropIfExists('blocked_signups');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks every invitation a user has sent — even if recipient hasn't signed up yet.
 * When recipient signs up via the referral link, `signed_up_at` and `signed_up_user_id`
 * are populated, and the row is considered "converted".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('invitee_email');
            $table->string('invitee_name')->nullable();
            $table->string('personal_message', 500)->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->foreignId('signed_up_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_up_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_user_id', 'created_at']);
            $table->index('invitee_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_invites');
    }
};

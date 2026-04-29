<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks invitations instructors send to learners. When the recipient clicks
 * the invite link and signs up (or is already a member), the invite is marked
 * as accepted + the learner is linked to the instructor for future bookings.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_learner_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('invitee_email');
            $table->string('invitee_name')->nullable();
            $table->text('personal_message')->nullable();
            $table->string('invite_token', 64)->unique();
            $table->timestamp('email_sent_at')->nullable();
            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['instructor_user_id', 'created_at']);
            $table->index('invitee_email');
            $table->index('accepted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_learner_invites');
    }
};

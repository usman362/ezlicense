<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_correspondences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();

            $table->enum('channel', ['email', 'sms', 'phone_call', 'in_person', 'system_message', 'other'])->default('email');
            $table->enum('direction', ['outbound', 'inbound'])->default('outbound');

            $table->string('subject', 255)->nullable();
            $table->longText('body');
            $table->json('attachments')->nullable(); // list of stored file paths

            // Link to a related record for context
            $table->foreignId('related_complaint_id')->nullable();
            $table->foreignId('related_warning_id')->nullable();
            $table->foreignId('related_block_id')->nullable();

            $table->timestamp('communicated_at')->nullable(); // when the actual communication happened
            $table->timestamps();

            $table->index(['instructor_profile_id', 'communicated_at'], 'ic_profile_comm_at_idx');
            $table->index('channel', 'ic_channel_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_correspondences');
    }
};

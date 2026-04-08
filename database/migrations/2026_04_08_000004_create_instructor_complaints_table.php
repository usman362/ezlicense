<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->cascadeOnDelete();

            // Who reported — can be a system user (learner) or a manually-entered name/contact
            $table->foreignId('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_name', 255)->nullable();
            $table->string('reporter_email', 255)->nullable();
            $table->string('reporter_phone', 50)->nullable();

            // Context
            $table->foreignId('booking_id')->nullable(); // intentionally no FK so deletion doesn't break history
            $table->enum('category', [
                'harassment',
                'safety',
                'misconduct',
                'no_show',
                'pricing_dispute',
                'vehicle_condition',
                'late',
                'unprofessional',
                'inappropriate_contact',
                'other',
            ])->default('other');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Content
            $table->string('subject', 255);
            $table->text('description');
            $table->json('attachments')->nullable(); // list of file paths

            // Status lifecycle
            $table->enum('status', ['open', 'investigating', 'resolved', 'dismissed', 'escalated'])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();

            // Legal/police escalation
            $table->boolean('police_reported')->default(false);
            $table->string('police_reference', 255)->nullable();
            $table->timestamp('police_reported_at')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete(); // admin who logged it
            $table->timestamps();

            $table->index(['instructor_profile_id', 'status']);
            $table->index('severity');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_complaints');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Public applications from prospective instructors.
 *
 * Flow:
 *   1. Visitor fills the public form (/apply-as-instructor)
 *      → row inserted here as `pending`. NO user account created.
 *   2. Admin reviews docs + decides approve / reject.
 *   3. On approve → InstructorInvite created, magic-link email sent.
 *      Applicant clicks link → sets password → account live (still needs
 *      docs upload + admin docs approval to take bookings).
 *   4. On reject → rejection email with reason.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('instructor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();         // SLA-20260603-AB12CD

            // Personal
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 191)->index();
            $table->string('phone', 30);

            // Bio
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->string('transmission', 10)->nullable();   // auto | manual | both
            $table->text('bio')->nullable();
            $table->foreignId('suburb_id')->nullable()->constrained('suburbs')->nullOnDelete();
            $table->decimal('lesson_price', 8, 2)->nullable();
            $table->string('vehicle_make', 60)->nullable();
            $table->string('vehicle_model', 60)->nullable();
            $table->unsignedSmallInteger('vehicle_year')->nullable();

            // Compliance documents — uploaded to Spaces, paths stored as JSON
            // e.g. ["driver_licence": "...", "instructor_certificate": "...", "wwcc": "...", "vehicle_rego": "...", "insurance": "..."]
            $table->json('documents')->nullable();

            // Status workflow
            $table->string('status', 30)->default('pending')->index();
            // pending → under_review → approved | rejected
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Audit trail
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_invite_id')->nullable()->constrained('instructor_invites')->nullOnDelete();
            $table->string('applied_ip', 45)->nullable();
            $table->string('applied_user_agent', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_applications');
    }
};

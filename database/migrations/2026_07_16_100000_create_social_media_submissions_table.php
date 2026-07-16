<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Instructor-submitted marketing material (learner test-pass testimonials).
 * An instructor uploads a short video + photos when a learner passes; the
 * submission lands in the admin panel to be posted on social media.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('instructor_profile_id')->nullable()->constrained('instructor_profiles')->nullOnDelete();

            $table->string('learner_name')->nullable();     // learner who passed
            $table->string('category')->default('drive_test'); // drive_test | lesson_milestone | other
            $table->date('test_date')->nullable();
            $table->text('caption')->nullable();            // instructor's words / experience

            $table->string('video_path')->nullable();       // Spaces path (private)
            $table->json('photo_paths')->nullable();        // array of Spaces paths

            $table->string('status')->default('pending')->index(); // pending | approved | posted | rejected
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_submissions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Top-level categories (e.g. Learners, Driving Instructors) ──
        Schema::create('support_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 60)->nullable();    // bootstrap-icons class
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // ── Sections within categories ──
        Schema::create('support_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('support_categories')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180);
            $table->text('description')->nullable();
            $table->string('icon', 60)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
            $table->index(['is_active', 'sort_order']);
        });

        // ── Articles within sections ──
        Schema::create('support_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('support_sections')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 280);
            $table->text('excerpt')->nullable();
            $table->longText('content');                          // HTML (sanitised)
            $table->string('meta_description', 500)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('helpful_yes_count')->default(0);
            $table->unsignedInteger('helpful_no_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'slug']);
            $table->index(['is_published', 'section_id']);
            $table->fullText(['title', 'content']);              // for search
        });

        // ── Per-article helpful feedback (deduped via IP) ──
        Schema::create('support_article_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('support_articles')->cascadeOnDelete();
            $table->boolean('is_helpful');
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'is_helpful']);
        });

        // ── Submitted support requests (mini-Zendesk inbox) ──
        Schema::create('support_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();           // SLR-20260531-000123
            $table->string('name', 150);
            $table->string('email', 191);
            $table->string('phone', 30)->nullable();
            $table->string('role', 40)->nullable();              // learner|instructor|other
            $table->string('topic', 80)->nullable();             // dropdown choice
            $table->string('subject', 255);
            $table->longText('message');
            $table->json('attachments')->nullable();             // future
            $table->string('status', 30)->default('new');        // new|open|pending|resolved|closed
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // if logged-in
            $table->text('admin_notes')->nullable();
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_requests');
        Schema::dropIfExists('support_article_feedback');
        Schema::dropIfExists('support_articles');
        Schema::dropIfExists('support_sections');
        Schema::dropIfExists('support_categories');
    }
};

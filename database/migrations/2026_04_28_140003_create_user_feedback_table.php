<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category'); // bug / suggestion / compliment / other
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5 (overall site rating)
            $table->text('message');
            $table->string('page_context')->nullable();   // URL where feedback came from
            $table->string('user_agent', 500)->nullable();
            $table->string('status')->default('new');      // new / reviewing / resolved / archived
            $table->text('admin_response')->nullable();
            $table->foreignId('responded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_feedback');
    }
};

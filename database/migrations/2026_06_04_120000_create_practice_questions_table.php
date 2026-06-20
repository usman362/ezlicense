<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('practice_questions', function (Blueprint $table) {
            $table->id();
            // 'general' = General Knowledge, 'road_safety' = Road Safety
            $table->string('section', 30)->default('general')->index();
            $table->text('question');
            $table->string('image_path', 500)->nullable();
            $table->json('options');           // array of answer strings
            $table->unsignedTinyInteger('correct_index')->default(0);
            $table->text('explanation')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_questions');
    }
};

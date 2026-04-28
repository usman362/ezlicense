<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_address');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->string('mailable_class')->nullable();
            $table->string('notification_class')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('sent'); // sent / failed
            $table->text('error_message')->nullable();
            $table->json('headers')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['to_address', 'created_at']);
            $table->index(['notification_class', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};

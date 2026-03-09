<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('suburb_id')->nullable()->constrained()->nullOnDelete(); // pickup location
            $table->string('type'); // lesson, test_package
            $table->string('transmission'); // auto, manual
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('test_pre_booked')->default(false); // learner has already booked test with authority
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled, no_show
            $table->text('learner_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['instructor_id', 'scheduled_at']);
            $table->index(['learner_id', 'scheduled_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

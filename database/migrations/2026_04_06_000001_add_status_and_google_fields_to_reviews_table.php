<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Admin moderation status: pending → approved / rejected
            $table->string('status', 20)->default('pending')->after('comment');

            // Track if learner was prompted to post on Google
            $table->boolean('google_review_prompted')->default(false)->after('is_hidden');

            // Admin rejection reason (optional)
            $table->string('rejection_reason', 500)->nullable()->after('status');

            // Timestamp when admin approved/rejected
            $table->timestamp('moderated_at')->nullable()->after('rejection_reason');

            // Admin who moderated
            $table->foreignId('moderated_by')->nullable()->after('moderated_at')
                ->constrained('users')->nullOnDelete();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropForeign(['moderated_by']);
            $table->dropColumn([
                'status',
                'google_review_prompted',
                'rejection_reason',
                'moderated_at',
                'moderated_by',
            ]);
        });
    }
};

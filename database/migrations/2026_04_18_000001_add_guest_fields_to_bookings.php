<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add guest booking support.
 *
 * Allows unauthenticated users to complete a booking first, then have
 * an account auto-created from their email after payment — matching the
 * EasyLicence flow. A guest booking is identified by guest_email being set
 * and learner_id being null UNTIL account creation links them up.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Guest contact details — captured at booking time before account exists
            $table->string('guest_name', 120)->nullable()->after('learner_id');
            $table->string('guest_email', 160)->nullable()->after('guest_name');
            $table->string('guest_phone', 30)->nullable()->after('guest_email');

            // Marks bookings made by a guest — helpful for reporting + welcome email
            $table->boolean('is_guest_booking')->default(false)->after('guest_phone');

            $table->index('guest_email');
            $table->index('is_guest_booking');
        });

        // Make learner_id nullable so guest bookings can exist without a user yet
        // We drop the foreign key, change column, then re-add FK with SET NULL on delete
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['learner_id']);
        });

        // Use raw SQL for the column change since it's portable across MySQL/MariaDB
        DB::statement('ALTER TABLE bookings MODIFY learner_id BIGINT UNSIGNED NULL');

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('learner_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['learner_id']);
        });

        // Best effort — only succeeds if no null learner_id rows exist
        try {
            DB::statement('ALTER TABLE bookings MODIFY learner_id BIGINT UNSIGNED NOT NULL');
        } catch (\Throwable $e) {
            // Leave nullable if rollback is not safe
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('learner_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();

            $table->dropIndex(['guest_email']);
            $table->dropIndex(['is_guest_booking']);
            $table->dropColumn(['guest_name', 'guest_email', 'guest_phone', 'is_guest_booking']);
        });
    }
};

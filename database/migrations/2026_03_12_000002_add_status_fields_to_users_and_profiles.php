<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });

        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->string('verification_status', 30)->default('pending')->after('is_active');
            // pending, documents_submitted, verified, rejected
            $table->text('admin_notes')->nullable()->after('verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'last_login_at']);
        });

        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'admin_notes']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // blocked_until: if set in the future, user is temporarily blocked.
            // If null AND is_active=false AND deactivation_reason='blocked', block is permanent.
            $table->timestamp('blocked_until')->nullable()->after('deactivated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('blocked_until');
        });
    }
};

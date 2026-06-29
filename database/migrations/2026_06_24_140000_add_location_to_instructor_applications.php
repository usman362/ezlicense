<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_applications', function (Blueprint $table) {
            // Where the applying instructor is based — so admins can see location before approving.
            $table->string('state', 60)->nullable()->after('phone');
            $table->string('postcode', 10)->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('instructor_applications', function (Blueprint $table) {
            $table->dropColumn(['state', 'postcode']);
        });
    }
};

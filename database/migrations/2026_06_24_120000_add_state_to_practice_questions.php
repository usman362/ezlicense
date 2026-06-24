<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practice_questions', function (Blueprint $table) {
            // Which state's learner test this question belongs to (nsw, vic, qld, ...).
            // NULL = "All states (common)" — shown in every state's test.
            $table->string('state', 10)->nullable()->index()->after('section');
        });
    }

    public function down(): void
    {
        Schema::table('practice_questions', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suburbs', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('postcode');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');

            $table->index(['latitude', 'longitude'], 'suburbs_coords_index');
        });
    }

    public function down(): void
    {
        Schema::table('suburbs', function (Blueprint $table) {
            $table->dropIndex('suburbs_coords_index');
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};

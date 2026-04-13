<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            // ── Vehicle details for the job ──────────────────────
            $table->foreignId('customer_vehicle_id')->nullable()->after('service_category_id')
                  ->constrained('customer_vehicles')->nullOnDelete();
            $table->string('vehicle_make', 60)->nullable()->after('customer_vehicle_id');
            $table->string('vehicle_model', 80)->nullable()->after('vehicle_make');
            $table->unsignedSmallInteger('vehicle_year')->nullable()->after('vehicle_model');
            $table->string('vehicle_registration', 20)->nullable()->after('vehicle_year');
            $table->json('vehicle_photos')->nullable()->after('vehicle_registration'); // array of photo paths

            // ── Mechanic service type ────────────────────────────
            $table->foreignId('mechanic_service_type_id')->nullable()->after('vehicle_photos')
                  ->constrained()->nullOnDelete();

            // ── Proposal / acceptance workflow ───────────────────
            $table->enum('proposal_status', ['pending', 'accepted', 'rejected', 'expired'])
                  ->default('pending')->after('cancellation_reason');
            $table->text('proposal_message')->nullable()->after('proposal_status');    // mechanic's response message
            $table->decimal('quoted_amount', 10, 2)->nullable()->after('proposal_message'); // mechanic's quote
            $table->timestamp('proposal_responded_at')->nullable()->after('quoted_amount');
            $table->timestamp('proposal_expires_at')->nullable()->after('proposal_responded_at');

            // ── Job completion proof (anti-chargeback) ───────────
            $table->json('completion_photos')->nullable()->after('proposal_expires_at'); // before/after photos
            $table->text('completion_notes')->nullable()->after('completion_photos');
            $table->timestamp('customer_confirmed_at')->nullable()->after('completion_notes');
            $table->string('customer_confirmed_ip', 45)->nullable()->after('customer_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_vehicle_id');
            $table->dropConstrainedForeignId('mechanic_service_type_id');
            $table->dropColumn([
                'vehicle_make', 'vehicle_model', 'vehicle_year', 'vehicle_registration',
                'vehicle_photos', 'proposal_status', 'proposal_message', 'quoted_amount',
                'proposal_responded_at', 'proposal_expires_at',
                'completion_photos', 'completion_notes',
                'customer_confirmed_at', 'customer_confirmed_ip',
            ]);
        });
    }
};

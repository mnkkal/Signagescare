<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('checkin_lat')->nullable();
            $table->string('checkin_lng')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->string('checkout_lat')->nullable();
            $table->string('checkout_lng')->nullable();
            $table->decimal('travel_distance_km', 8, 2)->nullable();
            $table->decimal('vehicle_cost', 10, 2)->nullable();
            $table->text('visit_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('safety_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('ppe_verified')->default(false);
            $table->boolean('electrical_isolation_confirmed')->default(false);
            $table->boolean('work_at_height_required')->default(false);
            $table->boolean('work_at_height_permit')->default(false);
            $table->string('permit_number')->nullable();
            $table->text('safety_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('safety_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('client_location_id')->nullable()->constrained('client_locations')->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('incident_type')->default('Near-Miss'); // Near-Miss, Minor Injury, Electrical Hazard, Fall Hazard, Property Damage
            $table->enum('severity', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->text('description');
            $table->text('corrective_action')->nullable();
            $table->date('incident_date');
            $table->enum('status', ['Open', 'Investigated', 'Closed'])->default('Open');
            $table->timestamps();
        });

        Schema::create('asset_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signage_asset_id')->constrained('signage_assets')->cascadeOnDelete();
            $table->string('document_type')->default('Municipal Permission'); // Municipal Permission, Electrical Test Certificate, Insurance Paper, Warranty Card, Job Card
            $table->string('title');
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('amc_contract_id')->nullable()->constrained('amc_contracts')->nullOnDelete();
            $table->enum('channel', ['WhatsApp', 'SMS', 'Email', 'System'])->default('WhatsApp');
            $table->string('recipient');
            $table->string('event_type'); // ticket_created, technician_assigned, work_completed, sla_escalation, amc_due
            $table->text('message');
            $table->enum('status', ['Queued', 'Sent', 'Delivered', 'Failed'])->default('Sent');
            $table->text('provider_response')->nullable();
            $table->timestamps();
        });

        Schema::create('mass_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code')->unique(); // e.g. INC-2026-001
            $table->string('title');
            $table->string('incident_type')->default('Storm'); // Storm, Monsoon, Grid Outage, Mall Shutdown, Other
            $table->text('description')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Active', 'Resolved'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mass_incidents');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('asset_documents');
        Schema::dropIfExists('safety_incidents');
        Schema::dropIfExists('safety_checklists');
        Schema::dropIfExists('ticket_visits');
    }
};

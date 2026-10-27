<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sop_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique(); // e.g. SOP-2026-001
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('client_location_id')->nullable()->constrained('client_locations')->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->string('title'); // e.g. Lenskart Dharampeth Complete Signage Revamp
            $table->enum('current_stage', [
                'sales_requirement',
                'site_survey_recce',
                'design_creative',
                'fabrication',
                'production',
                'production_workers',
                'packing_dispatch',
                'installation',
                'final_handover',
                'administrative_audit',
                'completed'
            ])->default('sales_requirement')->index();
            $table->enum('overall_status', ['Draft', 'In Progress', 'Under Review', 'Approved', 'Completed', 'On Hold'])->default('In Progress')->index();
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('target_deadline')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sop_projects');
    }
};

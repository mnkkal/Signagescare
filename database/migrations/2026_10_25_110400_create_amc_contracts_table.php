<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amc_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // e.g. AMC-2026-LKT-01
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('title'); // e.g. Multi-Location Annual Signage Maintenance
            $table->date('start_date');
            $table->date('end_date');
            $table->string('visit_frequency')->default('Quarterly'); // Monthly, Bi-Monthly, Quarterly, Bi-Annual
            $table->unsignedInteger('sla_response_hours')->default(24);
            $table->unsignedInteger('sla_resolution_hours')->default(48);
            $table->enum('status', ['active', 'expired', 'pending_renewal', 'terminated'])->default('active')->index();
            $table->decimal('contract_value', 12, 2)->nullable();
            $table->string('contract_doc_path')->nullable();
            $table->text('coverage_scope')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('amc_contract_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_contract_id')->constrained('amc_contracts')->cascadeOnDelete();
            $table->foreignId('client_location_id')->constrained('client_locations')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['amc_contract_id', 'client_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amc_contract_locations');
        Schema::dropIfExists('amc_contracts');
    }
};

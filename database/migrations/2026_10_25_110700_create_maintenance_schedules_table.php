<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_location_id')->constrained('client_locations')->cascadeOnDelete();
            $table->foreignId('amc_contract_id')->nullable()->constrained('amc_contracts')->nullOnDelete();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('scheduled_date');
            $table->date('last_visit_date')->nullable();
            $table->date('next_visit_date')->nullable();
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Overdue', 'Cancelled'])->default('Scheduled')->index();
            $table->text('checklist_summary')->nullable();
            $table->text('findings')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};

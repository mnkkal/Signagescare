<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g. SC-NAG-0048
            $table->foreignId('client_location_id')->constrained('client_locations')->cascadeOnDelete();
            $table->foreignId('signage_asset_id')->nullable()->constrained('signage_assets')->nullOnDelete();
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['P1', 'P2', 'P3', 'P4'])->default('P3')->index();
            $table->enum('status', [
                'Open',
                'Assigned',
                'In Progress',
                'Parts Required',
                'Scheduled',
                'Completed',
                'Overdue',
                'Cancelled'
            ])->default('Open')->index();
            $table->string('category')->default('Signage Issue'); // Main Sign OFF, 2 Letters OFF, Flickering, Cleaning, Sign Loose, Electrical Short
            $table->string('problem_title');
            $table->text('problem_description')->nullable();
            $table->date('eta_date')->nullable();
            $table->string('eta_display')->nullable(); // e.g. "Today", "Tomorrow", "12 Dec"
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sop_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sop_project_id')->constrained('sop_projects')->cascadeOnDelete();
            $table->string('stage'); // sales_requirement, site_survey_recce, etc.
            $table->string('section_title'); // 1. SALES / CLIENT REQUIREMENT, 2. SITE SURVEY & MEASUREMENT (RECCE), etc.
            $table->string('checklist_item');
            $table->enum('status', ['yes', 'no', 'na'])->default('na');
            $table->text('remarks')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index(['sop_project_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sop_checklists');
    }
};

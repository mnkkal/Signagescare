<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signage_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_location_id')->constrained('client_locations')->cascadeOnDelete();
            $table->string('asset_tag')->unique(); // e.g. AST-LKT-001
            $table->string('asset_name'); // e.g. Main Front Fascia LED, Side Blade Sign
            $table->string('sign_type')->default('Acrylic LED'); // Acrylic LED, ACP Backlit, Glowsign, Neon, Flex, Pylon
            $table->string('dimensions')->nullable(); // e.g. 24ft x 4ft
            $table->string('illumination_type')->default('Backlit'); // Frontlit, Backlit, Edge-lit, Non-lit
            $table->date('installation_date')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->enum('health_status', ['healthy', 'attention', 'critical'])->default('healthy')->index();
            $table->string('primary_image_path')->nullable();
            $table->json('specifications')->nullable(); // e.g. power supply wattage, LED brand, acrylic thickness
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signage_assets');
    }
};

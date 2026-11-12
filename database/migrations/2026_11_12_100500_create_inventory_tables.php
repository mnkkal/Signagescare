<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gstin')->nullable();
            $table->text('address')->nullable();
            $table->string('category')->default('Electrical & LEDs');
            $table->timestamps();
        });

        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category')->default('LEDs'); // LEDs, SMPS, Cables, Acrylic, ACP, Flex, Hardware
            $table->string('unit')->default('pcs'); // pcs, meters, sqft, roll
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ticket_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_parts');
        Schema::dropIfExists('spare_parts');
        Schema::dropIfExists('suppliers');
    }
};

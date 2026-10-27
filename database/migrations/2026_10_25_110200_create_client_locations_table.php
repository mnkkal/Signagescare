<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('name'); // e.g. Lenskart - Dharampeth, Lenskart - Sadar
            $table->string('store_code')->nullable()->index(); // e.g. LKT-NAG-001
            $table->string('city')->default('Nagpur');
            $table->string('state')->default('Maharashtra');
            $table->text('address')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('store_manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('primary_contact_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_locations');
    }
};

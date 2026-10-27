<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->foreignId('client_id')->nullable()->after('avatar')->constrained('clients')->nullOnDelete();
            $table->string('status')->default('active')->after('client_id'); // active, inactive, suspended
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['phone', 'avatar', 'client_id', 'status', 'last_login_at', 'last_login_ip']);
        });
    }
};

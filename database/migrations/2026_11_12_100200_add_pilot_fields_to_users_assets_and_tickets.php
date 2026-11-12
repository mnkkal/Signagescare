<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('skills')->nullable()->after('last_login_ip');
            $table->timestamp('consent_accepted_at')->nullable()->after('skills');
            $table->string('consent_ip', 45)->nullable()->after('consent_accepted_at');
        });

        Schema::table('signage_assets', function (Blueprint $table) {
            $table->string('manufacturer_model')->nullable()->after('illumination_type');
            $table->integer('power_rating_watts')->nullable()->after('manufacturer_model');
            $table->string('gps_coordinates')->nullable()->after('power_rating_watts');
            $table->foreignId('sop_project_id')->nullable()->after('gps_coordinates')->constrained('sop_projects')->nullOnDelete();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('billing_type', ['AMC Covered', 'Under Warranty', 'Chargeable'])->default('AMC Covered')->after('category');
            $table->decimal('estimated_amount', 12, 2)->nullable()->after('billing_type');
            $table->boolean('requires_client_approval')->default(false)->after('estimated_amount');
            $table->timestamp('client_approved_at')->nullable()->after('requires_client_approval');
            $table->foreignId('client_approved_by')->nullable()->after('client_approved_at')->constrained('users')->nullOnDelete();

            $table->timestamp('first_response_at')->nullable()->after('scheduled_at');
            $table->integer('response_sla_minutes')->default(240)->after('first_response_at'); // 4 hours default
            $table->integer('resolution_sla_minutes')->default(1440)->after('response_sla_minutes'); // 24 hours default
            $table->tinyInteger('escalation_level')->default(0)->after('resolution_sla_minutes');
            $table->timestamp('escalated_at')->nullable()->after('escalation_level');

            $table->string('root_cause')->nullable()->after('resolution_notes');
            $table->integer('reopen_count')->default(0)->after('root_cause');
            $table->text('reopen_reason')->nullable()->after('reopen_count');
            $table->timestamp('reopened_at')->nullable()->after('reopen_reason');

            $table->tinyInteger('client_rating')->nullable()->after('reopened_at');
            $table->text('client_feedback')->nullable()->after('client_rating');
            $table->timestamp('rated_at')->nullable()->after('client_feedback');

            $table->unsignedBigInteger('mass_incident_id')->nullable()->index()->after('signage_asset_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['client_approved_by']);
            $table->dropColumn([
                'billing_type', 'estimated_amount', 'requires_client_approval', 'client_approved_at', 'client_approved_by',
                'first_response_at', 'response_sla_minutes', 'resolution_sla_minutes', 'escalation_level', 'escalated_at',
                'root_cause', 'reopen_count', 'reopen_reason', 'reopened_at',
                'client_rating', 'client_feedback', 'rated_at', 'mass_incident_id'
            ]);
        });

        Schema::table('signage_assets', function (Blueprint $table) {
            $table->dropForeign(['sop_project_id']);
            $table->dropColumn(['manufacturer_model', 'power_rating_watts', 'gps_coordinates', 'sop_project_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skills', 'consent_accepted_at', 'consent_ip']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidate_service_data', function (Blueprint $table) {
            $table->index(['verification_status', 'verified_at'], 'idx_candidate_service_data_verification');
        });

        Schema::table('verification_results', function (Blueprint $table) {
            $table->index('candidate_service_data_id', 'idx_verification_results_candidate');
        });

        Schema::table('field_audit_log', function (Blueprint $table) {
            $table->index('candidate_service_data_id', 'idx_field_audit_candidate');
        });

        Schema::table('verification_reports', function (Blueprint $table) {
            $table->index('candidate_service_id', 'idx_verification_reports_candidate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_reports', function (Blueprint $table) {
            $table->dropIndex('idx_verification_reports_candidate');
        });

        Schema::table('field_audit_log', function (Blueprint $table) {
            $table->dropIndex('idx_field_audit_candidate');
        });

        Schema::table('verification_results', function (Blueprint $table) {
            $table->dropIndex('idx_verification_results_candidate');
        });

        Schema::table('candidate_service_data', function (Blueprint $table) {
            $table->dropIndex('idx_candidate_service_data_verification');
        });
    }
};

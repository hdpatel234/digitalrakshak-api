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
        Schema::table('service_provider_assignments', function (Blueprint $table) {
            $table->foreign(['service_id'], 'tblservice_provider_assignments_ibfk_1')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['provider_id'], 'tblservice_provider_assignments_ibfk_2')->references(['id'])->on('service_providers')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['provider_config_id'], 'tblservice_provider_assignments_ibfk_3')->references(['id'])->on('provider_api_configs')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_provider_assignments', function (Blueprint $table) {
            $table->dropForeign('tblservice_provider_assignments_ibfk_1');
            $table->dropForeign('tblservice_provider_assignments_ibfk_2');
            $table->dropForeign('tblservice_provider_assignments_ibfk_3');
        });
    }
};

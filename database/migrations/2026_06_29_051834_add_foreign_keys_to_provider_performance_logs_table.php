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
        Schema::table('provider_performance_logs', function (Blueprint $table) {
            $table->foreign(['provider_id'], 'tblprovider_performance_logs_ibfk_1')->references(['id'])->on('service_providers')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['service_id'], 'tblprovider_performance_logs_ibfk_2')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['assignment_id'], 'tblprovider_performance_logs_ibfk_3')->references(['id'])->on('service_provider_assignments')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_performance_logs', function (Blueprint $table) {
            $table->dropForeign('tblprovider_performance_logs_ibfk_1');
            $table->dropForeign('tblprovider_performance_logs_ibfk_2');
            $table->dropForeign('tblprovider_performance_logs_ibfk_3');
        });
    }
};

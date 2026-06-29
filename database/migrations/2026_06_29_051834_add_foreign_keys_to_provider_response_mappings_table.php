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
        Schema::table('provider_response_mappings', function (Blueprint $table) {
            $table->foreign(['service_provider_assignment_id'], 'tblprovider_response_mappings_ibfk_1')->references(['id'])->on('service_provider_assignments')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_response_mappings', function (Blueprint $table) {
            $table->dropForeign('tblprovider_response_mappings_ibfk_1');
        });
    }
};

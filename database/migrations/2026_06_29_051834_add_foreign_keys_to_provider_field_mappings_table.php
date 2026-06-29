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
        Schema::table('provider_field_mappings', function (Blueprint $table) {
            $table->foreign(['service_provider_assignment_id'], 'tblprovider_field_mappings_ibfk_1')->references(['id'])->on('service_provider_assignments')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['service_field_id'], 'tblprovider_field_mappings_ibfk_2')->references(['id'])->on('services_fields')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_field_mappings', function (Blueprint $table) {
            $table->dropForeign('tblprovider_field_mappings_ibfk_1');
            $table->dropForeign('tblprovider_field_mappings_ibfk_2');
        });
    }
};

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
        Schema::table('client_service_areas', function (Blueprint $table) {
            $table->foreign(['client_id'], 'tblclient_service_areas_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['country_id'], 'tblclient_service_areas_ibfk_2')->references(['id'])->on('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['state_id'], 'tblclient_service_areas_ibfk_3')->references(['id'])->on('states')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['city_id'], 'tblclient_service_areas_ibfk_4')->references(['id'])->on('cities')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_service_areas', function (Blueprint $table) {
            $table->dropForeign('tblclient_service_areas_ibfk_1');
            $table->dropForeign('tblclient_service_areas_ibfk_2');
            $table->dropForeign('tblclient_service_areas_ibfk_3');
            $table->dropForeign('tblclient_service_areas_ibfk_4');
        });
    }
};

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
        Schema::table('location_service_pricing', function (Blueprint $table) {
            $table->foreign(['client_id'], 'tbllocation_service_pricing_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['service_id'], 'tbllocation_service_pricing_ibfk_2')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['country_id'], 'tbllocation_service_pricing_ibfk_3')->references(['id'])->on('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['state_id'], 'tbllocation_service_pricing_ibfk_4')->references(['id'])->on('states')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['city_id'], 'tbllocation_service_pricing_ibfk_5')->references(['id'])->on('cities')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_service_pricing', function (Blueprint $table) {
            $table->dropForeign('tbllocation_service_pricing_ibfk_1');
            $table->dropForeign('tbllocation_service_pricing_ibfk_2');
            $table->dropForeign('tbllocation_service_pricing_ibfk_3');
            $table->dropForeign('tbllocation_service_pricing_ibfk_4');
            $table->dropForeign('tbllocation_service_pricing_ibfk_5');
        });
    }
};

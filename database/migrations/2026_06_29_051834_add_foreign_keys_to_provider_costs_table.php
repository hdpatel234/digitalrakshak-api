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
        Schema::table('provider_costs', function (Blueprint $table) {
            $table->foreign(['provider_id'], 'tblprovider_costs_ibfk_1')->references(['id'])->on('service_providers')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['service_id'], 'tblprovider_costs_ibfk_2')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_costs', function (Blueprint $table) {
            $table->dropForeign('tblprovider_costs_ibfk_1');
            $table->dropForeign('tblprovider_costs_ibfk_2');
        });
    }
};

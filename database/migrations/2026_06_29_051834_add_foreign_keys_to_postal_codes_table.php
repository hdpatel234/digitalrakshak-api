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
        Schema::table('postal_codes', function (Blueprint $table) {
            $table->foreign(['country_id'], 'tblpostal_codes_ibfk_1')->references(['id'])->on('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['state_id'], 'tblpostal_codes_ibfk_2')->references(['id'])->on('states')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['city_id'], 'tblpostal_codes_ibfk_3')->references(['id'])->on('cities')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postal_codes', function (Blueprint $table) {
            $table->dropForeign('tblpostal_codes_ibfk_1');
            $table->dropForeign('tblpostal_codes_ibfk_2');
            $table->dropForeign('tblpostal_codes_ibfk_3');
        });
    }
};

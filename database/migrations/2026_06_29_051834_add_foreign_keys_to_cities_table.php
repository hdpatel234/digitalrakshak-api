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
        Schema::table('cities', function (Blueprint $table) {
            $table->foreign(['state_id'], 'tblcities_ibfk_1')->references(['id'])->on('states')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['country_id'], 'tblcities_ibfk_2')->references(['id'])->on('countries')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign('tblcities_ibfk_1');
            $table->dropForeign('tblcities_ibfk_2');
        });
    }
};

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
        Schema::table('states', function (Blueprint $table) {
            $table->foreign(['country_id'], 'tblstates_ibfk_1')->references(['id'])->on('countries')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropForeign('tblstates_ibfk_1');
        });
    }
};

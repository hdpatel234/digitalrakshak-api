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
        Schema::table('user_config_values', function (Blueprint $table) {
            $table->foreign(['user_id'], 'tbluser_config_values_ibfk_1')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['config_id'], 'tbluser_config_values_ibfk_2')->references(['id'])->on('user_config_definitions')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_config_values', function (Blueprint $table) {
            $table->dropForeign('tbluser_config_values_ibfk_1');
            $table->dropForeign('tbluser_config_values_ibfk_2');
        });
    }
};

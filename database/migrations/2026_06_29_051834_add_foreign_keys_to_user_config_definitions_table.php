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
        Schema::table('user_config_definitions', function (Blueprint $table) {
            $table->foreign(['category_id'], 'tbluser_config_definitions_ibfk_1')->references(['id'])->on('user_config_categories')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_config_definitions', function (Blueprint $table) {
            $table->dropForeign('tbluser_config_definitions_ibfk_1');
        });
    }
};

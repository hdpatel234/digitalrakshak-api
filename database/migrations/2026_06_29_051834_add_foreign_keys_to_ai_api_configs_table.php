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
        Schema::table('ai_api_configs', function (Blueprint $table) {
            $table->foreign(['provider_id'], 'tblai_api_configs_ibfk_1')->references(['id'])->on('ai_providers')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['model_id'], 'tblai_api_configs_ibfk_2')->references(['id'])->on('ai_models')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_api_configs', function (Blueprint $table) {
            $table->dropForeign('tblai_api_configs_ibfk_1');
            $table->dropForeign('tblai_api_configs_ibfk_2');
        });
    }
};

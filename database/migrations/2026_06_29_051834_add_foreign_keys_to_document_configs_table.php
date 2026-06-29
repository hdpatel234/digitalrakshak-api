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
        Schema::table('document_configs', function (Blueprint $table) {
            $table->foreign(['document_platform_id'], 'tbldocument_configs_ibfk_2')->references(['id'])->on('document_platforms')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_configs', function (Blueprint $table) {
            $table->dropForeign('tbldocument_configs_ibfk_2');
        });
    }
};

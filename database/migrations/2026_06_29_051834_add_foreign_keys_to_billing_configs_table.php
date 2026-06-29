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
        Schema::table('billing_configs', function (Blueprint $table) {
            $table->foreign(['billing_platform_id'], 'tblbilling_configs_ibfk_2')->references(['id'])->on('billing_platforms')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_configs', function (Blueprint $table) {
            $table->dropForeign('tblbilling_configs_ibfk_2');
        });
    }
};

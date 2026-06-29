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
        Schema::table('support_configs', function (Blueprint $table) {
            $table->foreign(['support_platform_id'], 'tblsupport_configs_ibfk_2')->references(['id'])->on('support_platforms')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_configs', function (Blueprint $table) {
            $table->dropForeign('tblsupport_configs_ibfk_2');
        });
    }
};

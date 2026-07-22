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
        Schema::table('verification_workflow_configs', function (Blueprint $table) {
            $table->foreign(['service_id'])->references(['id'])->on('services')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_workflow_configs', function (Blueprint $table) {
            $table->dropForeign('tblverification_workflow_configs_service_id_foreign');
        });
    }
};

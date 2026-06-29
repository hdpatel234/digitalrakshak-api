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
        Schema::table('service_processing_rules', function (Blueprint $table) {
            $table->foreign(['service_id'], 'tblservice_processing_rules_ibfk_1')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_processing_rules', function (Blueprint $table) {
            $table->dropForeign('tblservice_processing_rules_ibfk_1');
        });
    }
};

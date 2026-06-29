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
        Schema::table('service_dependencies', function (Blueprint $table) {
            $table->foreign(['service_id'], 'tblservice_dependencies_ibfk_1')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['depends_on_service_id'], 'tblservice_dependencies_ibfk_2')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_dependencies', function (Blueprint $table) {
            $table->dropForeign('tblservice_dependencies_ibfk_1');
            $table->dropForeign('tblservice_dependencies_ibfk_2');
        });
    }
};

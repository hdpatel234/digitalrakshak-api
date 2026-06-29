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
        Schema::table('api_logs', function (Blueprint $table) {
            $table->foreign(['service_id'], 'tblapi_logs_ibfk_1')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['order_item_id'], 'tblapi_logs_ibfk_2')->references(['id'])->on('order_items')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_logs', function (Blueprint $table) {
            $table->dropForeign('tblapi_logs_ibfk_1');
            $table->dropForeign('tblapi_logs_ibfk_2');
        });
    }
};

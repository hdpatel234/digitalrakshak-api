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
        Schema::table('service_processing_queue', function (Blueprint $table) {
            $table->foreign(['order_item_id'], 'tblservice_processing_queue_ibfk_1')->references(['id'])->on('order_items')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['service_id'], 'tblservice_processing_queue_ibfk_2')->references(['id'])->on('services')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['candidate_id'], 'tblservice_processing_queue_ibfk_3')->references(['id'])->on('candidates')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['processing_rule_id'], 'tblservice_processing_queue_ibfk_4')->references(['id'])->on('service_processing_rules')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_processing_queue', function (Blueprint $table) {
            $table->dropForeign('tblservice_processing_queue_ibfk_1');
            $table->dropForeign('tblservice_processing_queue_ibfk_2');
            $table->dropForeign('tblservice_processing_queue_ibfk_3');
            $table->dropForeign('tblservice_processing_queue_ibfk_4');
        });
    }
};

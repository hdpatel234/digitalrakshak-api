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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign(['processing_rule_id'], 'tblorder_items_ibfk_1')->references(['id'])->on('service_processing_rules')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['support_config_id'], 'tblorder_items_ibfk_2')->references(['id'])->on('support_configs')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['ticket_id'], 'tblorder_items_ibfk_3')->references(['id'])->on('support_tickets')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['report_document_id'], 'tblorder_items_ibfk_4')->references(['id'])->on('documents')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('tblorder_items_ibfk_1');
            $table->dropForeign('tblorder_items_ibfk_2');
            $table->dropForeign('tblorder_items_ibfk_3');
            $table->dropForeign('tblorder_items_ibfk_4');
        });
    }
};

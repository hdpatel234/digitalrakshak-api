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
        Schema::table('candidate_services', function (Blueprint $table) {
            $table->foreign(['order_id'], 'tblcandidate_services_ibfk_1')->references(['id'])->on('candidate_orders')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['order_item_id'], 'tblcandidate_services_ibfk_2')->references(['id'])->on('order_items')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['processing_rule_id'], 'tblcandidate_services_ibfk_3')->references(['id'])->on('service_processing_rules')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_services', function (Blueprint $table) {
            $table->dropForeign('tblcandidate_services_ibfk_1');
            $table->dropForeign('tblcandidate_services_ibfk_2');
            $table->dropForeign('tblcandidate_services_ibfk_3');
        });
    }
};

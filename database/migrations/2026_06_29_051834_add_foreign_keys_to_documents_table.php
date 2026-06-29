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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign(['client_id'], 'tbldocuments_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['document_config_id'], 'tbldocuments_ibfk_2')->references(['id'])->on('document_configs')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['candidate_id'], 'tbldocuments_ibfk_3')->references(['id'])->on('candidates')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['order_id'], 'tbldocuments_ibfk_4')->references(['id'])->on('candidate_orders')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['order_item_id'], 'tbldocuments_ibfk_5')->references(['id'])->on('order_items')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['invitation_id'], 'tbldocuments_ibfk_6')->references(['id'])->on('candidate_invitations')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['ticket_id'], 'tbldocuments_ibfk_7')->references(['id'])->on('support_tickets')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign('tbldocuments_ibfk_1');
            $table->dropForeign('tbldocuments_ibfk_2');
            $table->dropForeign('tbldocuments_ibfk_3');
            $table->dropForeign('tbldocuments_ibfk_4');
            $table->dropForeign('tbldocuments_ibfk_5');
            $table->dropForeign('tbldocuments_ibfk_6');
            $table->dropForeign('tbldocuments_ibfk_7');
        });
    }
};

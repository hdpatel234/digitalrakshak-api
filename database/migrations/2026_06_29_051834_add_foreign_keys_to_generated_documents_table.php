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
        Schema::table('generated_documents', function (Blueprint $table) {
            $table->foreign(['template_id'], 'tblgenerated_documents_ibfk_1')->references(['id'])->on('document_templates')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['client_id'], 'tblgenerated_documents_ibfk_2')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['document_config_id'], 'tblgenerated_documents_ibfk_3')->references(['id'])->on('document_configs')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['document_id'], 'tblgenerated_documents_ibfk_4')->references(['id'])->on('documents')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_documents', function (Blueprint $table) {
            $table->dropForeign('tblgenerated_documents_ibfk_1');
            $table->dropForeign('tblgenerated_documents_ibfk_2');
            $table->dropForeign('tblgenerated_documents_ibfk_3');
            $table->dropForeign('tblgenerated_documents_ibfk_4');
        });
    }
};

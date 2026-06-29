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
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['default_billing_config_id'], 'tblclients_ibfk_1')->references(['id'])->on('billing_platforms')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['default_support_config_id'], 'tblclients_ibfk_2')->references(['id'])->on('support_platforms')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['default_document_config_id'], 'tblclients_ibfk_3')->references(['id'])->on('document_platforms')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['country_id'], 'tblclients_ibfk_4')->references(['id'])->on('countries')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['state_id'], 'tblclients_ibfk_5')->references(['id'])->on('states')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['city_id'], 'tblclients_ibfk_6')->references(['id'])->on('cities')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('tblclients_ibfk_1');
            $table->dropForeign('tblclients_ibfk_2');
            $table->dropForeign('tblclients_ibfk_3');
            $table->dropForeign('tblclients_ibfk_4');
            $table->dropForeign('tblclients_ibfk_5');
            $table->dropForeign('tblclients_ibfk_6');
        });
    }
};

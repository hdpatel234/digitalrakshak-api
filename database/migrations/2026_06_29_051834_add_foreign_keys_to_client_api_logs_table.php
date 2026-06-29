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
        Schema::table('client_api_logs', function (Blueprint $table) {
            $table->foreign(['client_id'], 'tblclient_api_logs_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['api_key_id'], 'tblclient_api_logs_ibfk_2')->references(['id'])->on('client_api_keys')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_api_logs', function (Blueprint $table) {
            $table->dropForeign('tblclient_api_logs_ibfk_1');
            $table->dropForeign('tblclient_api_logs_ibfk_2');
        });
    }
};

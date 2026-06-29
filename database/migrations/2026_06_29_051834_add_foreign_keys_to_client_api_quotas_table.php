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
        Schema::table('client_api_quotas', function (Blueprint $table) {
            $table->foreign(['client_id'], 'tblclient_api_quotas_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_api_quotas', function (Blueprint $table) {
            $table->dropForeign('tblclient_api_quotas_ibfk_1');
        });
    }
};

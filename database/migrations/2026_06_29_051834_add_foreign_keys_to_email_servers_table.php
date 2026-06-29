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
        Schema::table('email_servers', function (Blueprint $table) {
            $table->foreign(['server_type_id'], 'tblemail_servers_ibfk_1')->references(['id'])->on('email_server_types')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_servers', function (Blueprint $table) {
            $table->dropForeign('tblemail_servers_ibfk_1');
        });
    }
};

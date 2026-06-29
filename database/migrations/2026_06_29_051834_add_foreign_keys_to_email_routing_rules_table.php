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
        Schema::table('email_routing_rules', function (Blueprint $table) {
            $table->foreign(['server_id'], 'tblemail_routing_rules_ibfk_1')->references(['id'])->on('email_servers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['failover_server_id'], 'tblemail_routing_rules_ibfk_2')->references(['id'])->on('email_servers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['client_id'], 'tblemail_routing_rules_ibfk_3')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_routing_rules', function (Blueprint $table) {
            $table->dropForeign('tblemail_routing_rules_ibfk_1');
            $table->dropForeign('tblemail_routing_rules_ibfk_2');
            $table->dropForeign('tblemail_routing_rules_ibfk_3');
        });
    }
};

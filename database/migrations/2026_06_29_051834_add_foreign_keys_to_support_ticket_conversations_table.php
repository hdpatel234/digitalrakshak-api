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
        Schema::table('support_ticket_conversations', function (Blueprint $table) {
            $table->foreign(['ticket_id'], 'tblsupport_ticket_conversations_ibfk_1')->references(['id'])->on('support_tickets')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_ticket_conversations', function (Blueprint $table) {
            $table->dropForeign('tblsupport_ticket_conversations_ibfk_1');
        });
    }
};

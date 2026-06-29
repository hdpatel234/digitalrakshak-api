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
        Schema::table('email_logs', function (Blueprint $table) {
            $table->foreign(['email_queue_id'], 'tblemail_logs_ibfk_1')->references(['id'])->on('email_queue')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['server_id'], 'tblemail_logs_ibfk_2')->references(['id'])->on('email_servers')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropForeign('tblemail_logs_ibfk_1');
            $table->dropForeign('tblemail_logs_ibfk_2');
        });
    }
};

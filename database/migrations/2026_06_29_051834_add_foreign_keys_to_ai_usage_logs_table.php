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
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            $table->foreign(['config_id'], 'tblai_usage_logs_ibfk_1')->references(['id'])->on('ai_api_configs')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['model_id'], 'tblai_usage_logs_ibfk_2')->references(['id'])->on('ai_models')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'], 'tblai_usage_logs_ibfk_3')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['client_id'], 'tblai_usage_logs_ibfk_4')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['conversation_id'], 'tblai_usage_logs_ibfk_5')->references(['id'])->on('ai_conversations')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['message_id'], 'tblai_usage_logs_ibfk_6')->references(['id'])->on('ai_messages')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            $table->dropForeign('tblai_usage_logs_ibfk_1');
            $table->dropForeign('tblai_usage_logs_ibfk_2');
            $table->dropForeign('tblai_usage_logs_ibfk_3');
            $table->dropForeign('tblai_usage_logs_ibfk_4');
            $table->dropForeign('tblai_usage_logs_ibfk_5');
            $table->dropForeign('tblai_usage_logs_ibfk_6');
        });
    }
};

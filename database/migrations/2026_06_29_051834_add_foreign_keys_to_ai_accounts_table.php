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
        Schema::table('ai_accounts', function (Blueprint $table) {
            $table->foreign(['provider_id'])->references(['id'])->on('ai_providers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_accounts', function (Blueprint $table) {
            $table->dropForeign('tblai_accounts_provider_id_foreign');
        });
    }
};

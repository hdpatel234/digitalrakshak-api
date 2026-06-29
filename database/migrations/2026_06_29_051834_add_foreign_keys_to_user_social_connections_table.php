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
        Schema::table('user_social_connections', function (Blueprint $table) {
            $table->foreign(['user_id'], 'tbluser_social_connections_ibfk_1')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['provider_id'], 'tbluser_social_connections_ibfk_2')->references(['id'])->on('social_login_providers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_social_connections', function (Blueprint $table) {
            $table->dropForeign('tbluser_social_connections_ibfk_1');
            $table->dropForeign('tbluser_social_connections_ibfk_2');
        });
    }
};

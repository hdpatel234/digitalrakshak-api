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
        Schema::table('social_login_attempts', function (Blueprint $table) {
            $table->foreign(['provider_id'], 'tblsocial_login_attempts_ibfk_1')->references(['id'])->on('social_login_providers')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_login_attempts', function (Blueprint $table) {
            $table->dropForeign('tblsocial_login_attempts_ibfk_1');
        });
    }
};

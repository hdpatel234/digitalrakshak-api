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
        Schema::table('user_recent_items', function (Blueprint $table) {
            $table->foreign(['user_id'], 'tbluser_recent_items_ibfk_1')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_recent_items', function (Blueprint $table) {
            $table->dropForeign('tbluser_recent_items_ibfk_1');
        });
    }
};

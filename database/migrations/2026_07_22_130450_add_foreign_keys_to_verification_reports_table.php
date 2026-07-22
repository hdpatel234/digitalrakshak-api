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
        Schema::table('verification_reports', function (Blueprint $table) {
            $table->foreign(['order_item_id'])->references(['id'])->on('order_items')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_reports', function (Blueprint $table) {
            $table->dropForeign('tblverification_reports_order_item_id_foreign');
        });
    }
};

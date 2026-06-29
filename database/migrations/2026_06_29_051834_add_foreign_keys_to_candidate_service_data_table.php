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
        Schema::table('candidate_service_data', function (Blueprint $table) {
            $table->foreign(['document_id'], 'tblcandidate_service_data_ibfk_1')->references(['id'])->on('documents')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_service_data', function (Blueprint $table) {
            $table->dropForeign('tblcandidate_service_data_ibfk_1');
        });
    }
};

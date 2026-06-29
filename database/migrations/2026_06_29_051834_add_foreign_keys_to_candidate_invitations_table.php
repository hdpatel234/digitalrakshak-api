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
        Schema::table('candidate_invitations', function (Blueprint $table) {
            $table->foreign(['package_id'], 'tblcandidate_invitations_ibfk_1')->references(['id'])->on('packages')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_invitations', function (Blueprint $table) {
            $table->dropForeign('tblcandidate_invitations_ibfk_1');
        });
    }
};

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
        Schema::table('billing_service_mappings', function (Blueprint $table) {
            $table->foreign(['billing_platform_id'])->references(['id'])->on('billing_platforms')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['package_id'])->references(['id'])->on('packages')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_service_mappings', function (Blueprint $table) {
            $table->dropForeign('tblbilling_service_mappings_billing_platform_id_foreign');
            $table->dropForeign('tblbilling_service_mappings_created_by_foreign');
            $table->dropForeign('tblbilling_service_mappings_package_id_foreign');
            $table->dropForeign('tblbilling_service_mappings_updated_by_foreign');
        });
    }
};

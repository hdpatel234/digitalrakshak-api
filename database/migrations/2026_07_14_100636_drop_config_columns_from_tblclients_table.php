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
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'default_billing_config_id')) {
                $table->dropColumn('default_billing_config_id');
            }
            if (Schema::hasColumn('clients', 'default_support_config_id')) {
                $table->dropColumn('default_support_config_id');
            }
            if (Schema::hasColumn('clients', 'default_document_config_id')) {
                $table->dropColumn('default_document_config_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'default_billing_config_id')) {
                $table->unsignedBigInteger('default_billing_config_id')->nullable();
            }
            if (!Schema::hasColumn('clients', 'default_support_config_id')) {
                $table->unsignedBigInteger('default_support_config_id')->nullable();
            }
            if (!Schema::hasColumn('clients', 'default_document_config_id')) {
                $table->unsignedBigInteger('default_document_config_id')->nullable();
            }
        });
    }
};

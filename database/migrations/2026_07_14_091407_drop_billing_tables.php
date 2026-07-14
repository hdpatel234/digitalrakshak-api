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
        try {
            $prefix = \Illuminate\Support\Facades\DB::getTablePrefix();
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$prefix}clients` DROP FOREIGN KEY `tblclients_ibfk_1`");
        } catch (\Exception $e) {
            // Ignore missing foreign key
        }

        if (Schema::hasColumn('clients', 'default_billing_config_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('default_billing_config_id');
            });
        }

        Schema::dropIfExists('billing_service_mappings');
        Schema::dropIfExists('billing_configs');
        Schema::dropIfExists('billing_platforms');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this migration is not supported since the module is permanently removed.
    }
};

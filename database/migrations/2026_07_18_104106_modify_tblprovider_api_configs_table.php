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
        Schema::table('provider_api_configs', function (Blueprint $table) {
            if (Schema::hasColumn('provider_api_configs', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('provider_api_configs', 'is_defualt')) {
                $table->dropColumn('is_defualt');
            }
            if (!Schema::hasColumn('provider_api_configs', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('failed_calls');
            }
            if (!Schema::hasColumn('provider_api_configs', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            }
            if (!Schema::hasColumn('provider_api_configs', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_api_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_api_configs', 'is_active')) {
                $table->boolean('is_active')->default(1);
            }
            if (!Schema::hasColumn('provider_api_configs', 'is_defualt')) {
                $table->boolean('is_defualt')->default(0);
            }
            if (Schema::hasColumn('provider_api_configs', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('provider_api_configs', 'deleted_by')) {
                $table->dropColumn('deleted_by');
            }
            if (Schema::hasColumn('provider_api_configs', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};

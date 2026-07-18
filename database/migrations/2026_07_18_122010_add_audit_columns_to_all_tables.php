<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $databaseName = DB::connection()->getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $key = "Tables_in_{$databaseName}";
        $prefix = DB::connection()->getTablePrefix();

        foreach ($tables as $table) {
            $tableName = $table->$key ?? ((array)$table)[array_key_first((array)$table)];
            
            // Exclude cache tables, migrations table, and other system tables
            if (str_starts_with($tableName, 'tblcache') || in_array($tableName, ['migrations', 'password_reset_tokens', 'failed_jobs', 'personal_access_tokens'])) {
                continue;
            }

            $unprefixedTableName = $tableName;
            if ($prefix && str_starts_with($tableName, $prefix)) {
                $unprefixedTableName = substr($tableName, strlen($prefix));
            }

            Schema::table($unprefixedTableName, function (Blueprint $table) use ($unprefixedTableName) {
                if (!Schema::hasColumn($unprefixedTableName, 'created_at')) {
                    $table->timestamp('created_at')->nullable()->useCurrent();
                }
                
                if (!Schema::hasColumn($unprefixedTableName, 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
                }
                
                if (!Schema::hasColumn($unprefixedTableName, 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable();
                }
                
                if (!Schema::hasColumn($unprefixedTableName, 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                
                if (!Schema::hasColumn($unprefixedTableName, 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                }
                
                if (!Schema::hasColumn($unprefixedTableName, 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversing this operation is risky as it applies to all tables and could drop columns
        // that previously existed before this migration. Thus, left empty for safety.
    }
};

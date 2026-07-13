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
        $tableName = DB::getTablePrefix() . 'users';

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('last_login_provider_id');
        });

        // Migrate data
        DB::statement("UPDATE {$tableName} SET status = IF(is_active = 1, 'active', 'inactive')");
        DB::statement("UPDATE {$tableName} SET user_type = 'admin_user' WHERE user_type = 'admin'");

        // Modify user_type enum and drop old columns
        DB::statement("ALTER TABLE {$tableName} MODIFY COLUMN user_type ENUM('super_admin', 'client_admin', 'client_user', 'admin_user') DEFAULT 'client_user'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = DB::getTablePrefix() . 'users';

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('last_login_provider_id');
            $table->boolean('is_admin')->default(false)->after('is_active');
        });

        DB::statement("UPDATE {$tableName} SET is_active = IF(status = 'active', 1, 0)");
        
        DB::statement("ALTER TABLE {$tableName} MODIFY COLUMN user_type ENUM('super_admin', 'admin', 'client_admin', 'client_user', 'admin_user') DEFAULT 'client_user'");
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

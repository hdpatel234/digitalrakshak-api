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
        DB::statement("ALTER TABLE tblusers MODIFY COLUMN user_type ENUM('super_admin', 'admin', 'client_admin', 'client_user', 'admin_user') DEFAULT 'client_user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tblusers MODIFY COLUMN user_type ENUM('super_admin', 'admin', 'client_admin', 'client_user') DEFAULT 'client_user'");
    }
};

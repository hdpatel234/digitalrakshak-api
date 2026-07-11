<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE tblemail_queue MODIFY COLUMN status ENUM('pending','processing','sent','failed','bounced','cancelled','retried') DEFAULT 'pending'");
        DB::statement("ALTER TABLE tblemail_logs MODIFY COLUMN status ENUM('sent','failed','bounced','opened','clicked','retried') DEFAULT 'sent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tblemail_queue MODIFY COLUMN status ENUM('pending','processing','sent','failed','bounced','cancelled') DEFAULT 'pending'");
        DB::statement("ALTER TABLE tblemail_logs MODIFY COLUMN status ENUM('sent','failed','bounced','opened','clicked') DEFAULT 'sent'");
    }
};

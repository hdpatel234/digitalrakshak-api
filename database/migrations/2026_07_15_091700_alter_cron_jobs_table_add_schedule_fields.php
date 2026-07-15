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
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->string('command')->after('job_name')->nullable();
            $table->string('schedule')->after('command')->nullable();
            $table->boolean('is_active')->default(true)->after('schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->dropColumn(['command', 'schedule', 'is_active']);
        });
    }
};

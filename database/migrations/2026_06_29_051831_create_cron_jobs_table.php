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
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_name')->unique('cron_job_name');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->enum('status', ['running', 'completed', 'failed'])->nullable()->default('completed');
            $table->integer('duration_seconds')->nullable();
            $table->integer('processed_count')->nullable()->default(0);
            $table->integer('error_count')->nullable()->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_jobs');
    }
};

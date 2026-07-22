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
        Schema::create('cron_job_executions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_key');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('running');
            $table->string('triggered_by')->default('system');
            $table->unsignedBigInteger('triggered_by_user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->longText('output')->nullable();
            $table->text('error_message')->nullable();
            $table->longText('error_stack')->nullable();
            $table->integer('processed_count')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->longText('processed_logs')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_job_executions');
    }
};

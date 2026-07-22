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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_queue_id')->nullable()->index('email_queue_id');
            $table->unsignedBigInteger('server_id')->index('server_id');
            $table->enum('status', ['sent', 'failed', 'bounced', 'opened', 'clicked', 'retried'])->nullable()->default('sent')->index('email_logs_status');
            $table->longText('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('email_logs_created');
            $table->timestamp('updated_at')->nullable()->useCurrent();
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
        Schema::dropIfExists('email_logs');
    }
};

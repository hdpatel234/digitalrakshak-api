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
            $table->string('email_uid', 100);
            $table->string('to_email', 500);
            $table->string('subject', 998);
            $table->unsignedBigInteger('server_id')->index('server_id');
            $table->string('message_id')->nullable()->index('email_logs_message');
            $table->enum('status', ['sent', 'failed', 'bounced', 'opened', 'clicked'])->nullable()->default('sent')->index('email_logs_status');
            $table->longText('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('opens')->nullable()->default(0);
            $table->integer('clicks')->nullable()->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('email_logs_created');
            $table->timestamp('updated_at')->nullable()->useCurrent();
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

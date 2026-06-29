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
        Schema::create('email_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_uid', 100)->unique('email_uid');
            $table->string('to_email', 500);
            $table->string('to_name')->nullable();
            $table->longText('cc')->nullable();
            $table->longText('bcc')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('subject', 998);
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('email_type', 100)->nullable()->index('email_queue_type');
            $table->enum('priority', ['high', 'normal', 'low'])->nullable()->default('normal');
            $table->unsignedBigInteger('client_id')->nullable()->index('client_id');
            $table->unsignedBigInteger('candidate_id')->nullable()->index('candidate_id');
            $table->unsignedBigInteger('order_id')->nullable()->index('order_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('assigned_server_id')->nullable()->index('assigned_server_id');
            $table->unsignedBigInteger('routing_rule_id')->nullable()->index('routing_rule_id');
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'bounced', 'cancelled'])->nullable()->default('pending');
            $table->integer('attempts')->nullable()->default(0);
            $table->integer('max_attempts')->nullable()->default(3);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('message_id')->nullable()->index('email_queue_message');
            $table->longText('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('opens')->nullable()->default(0);
            $table->integer('clicks')->nullable()->default(0);
            $table->timestamp('last_opened_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['status', 'priority', 'scheduled_at'], 'email_queue_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_queue');
    }
};

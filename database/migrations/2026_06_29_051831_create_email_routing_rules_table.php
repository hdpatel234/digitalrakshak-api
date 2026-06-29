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
        Schema::create('email_routing_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rule_name');
            $table->integer('rule_priority')->nullable()->default(0);
            $table->boolean('is_active')->nullable()->default(true);
            $table->enum('match_type', ['email_type', 'to_domain', 'to_email', 'from_domain', 'from_email', 'client', 'all']);
            $table->string('match_value', 500)->nullable();
            $table->string('match_pattern', 500)->nullable();
            $table->enum('email_type', ['candidate_invitation', 'verification_notification', 'order_confirmation', 'invoice', 'support_ticket', 'password_reset', 'welcome_email', 'reminder', 'report', 'alert', 'newsletter', 'system_notification', 'test'])->nullable()->index('email_rules_type');
            $table->enum('action_type', ['use_server', 'use_group', 'failover', 'round_robin', 'random'])->nullable()->default('use_server');
            $table->unsignedBigInteger('server_id')->nullable()->index('server_id');
            $table->string('server_group', 100)->nullable();
            $table->unsignedBigInteger('failover_server_id')->nullable()->index('failover_server_id');
            $table->integer('max_retries')->nullable()->default(3);
            $table->integer('retry_delay_seconds')->nullable()->default(60);
            $table->unsignedBigInteger('client_id')->nullable()->index('email_rules_client');
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->longText('days_of_week')->nullable();
            $table->bigInteger('times_used')->nullable()->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['rule_priority', 'is_active'], 'email_rules_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_routing_rules');
    }
};

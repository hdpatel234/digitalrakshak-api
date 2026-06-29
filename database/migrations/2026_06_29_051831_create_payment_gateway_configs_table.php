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
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gateway_id');
            $table->string('config_name');
            $table->enum('environment', ['production', 'sandbox', 'testing', 'development'])->nullable()->default('production');
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('api_token')->nullable();
            $table->string('merchant_id')->nullable();
            $table->text('merchant_key')->nullable();
            $table->text('salt')->nullable();
            $table->string('base_url', 500)->nullable();
            $table->string('webhook_url', 500)->nullable();
            $table->string('callback_url', 500)->nullable();
            $table->string('redirect_url', 500)->nullable();
            $table->longText('enabled_methods')->nullable();
            $table->longText('currencies')->nullable();
            $table->decimal('min_amount', 10)->nullable();
            $table->decimal('max_amount', 10)->nullable();
            $table->enum('transaction_fee_type', ['fixed', 'percentage', 'both'])->nullable()->default('percentage');
            $table->decimal('transaction_fee_fixed', 10)->nullable()->default(0);
            $table->decimal('transaction_fee_percentage', 5)->nullable()->default(0);
            $table->decimal('setup_fee', 10)->nullable()->default(0);
            $table->decimal('annual_fee', 10)->nullable()->default(0);
            $table->enum('settlement_cycle', ['instant', 'daily', 't+1', 't+2', 'weekly', 'monthly'])->nullable()->default('t+1');
            $table->string('settlement_bank')->nullable();
            $table->string('settlement_account')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_sandbox')->nullable()->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->enum('health_status', ['healthy', 'unhealthy', 'degraded'])->nullable()->default('healthy');
            $table->integer('error_count')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['gateway_id', 'environment', 'is_active'], 'payment_gateway_configs_env');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};

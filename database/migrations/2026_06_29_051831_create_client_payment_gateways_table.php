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
        Schema::create('client_payment_gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('gateway_config_id');
            $table->string('display_name')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->longText('enabled_methods')->nullable();
            $table->longText('currencies')->nullable();
            $table->enum('fee_type', ['global', 'fixed', 'percentage', 'both'])->nullable()->default('global');
            $table->decimal('fee_fixed', 10)->nullable();
            $table->decimal('fee_percentage', 5)->nullable();
            $table->decimal('min_amount', 10)->nullable();
            $table->decimal('max_amount', 10)->nullable();
            $table->decimal('daily_limit', 10)->nullable();
            $table->decimal('monthly_limit', 10)->nullable();
            $table->boolean('is_enabled')->nullable()->default(false);
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_mandatory')->nullable()->default(false);
            $table->integer('total_transactions')->nullable()->default(0);
            $table->decimal('total_amount', 15)->nullable()->default(0);
            $table->timestamp('last_transaction_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['client_id', 'is_enabled', 'display_order'], 'client_payment_gateways_enabled');
            $table->unique(['client_id', 'gateway_config_id'], 'unique_client_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_payment_gateways');
    }
};

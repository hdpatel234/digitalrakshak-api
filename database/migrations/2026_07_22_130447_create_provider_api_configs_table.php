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
        Schema::create('service_provider_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id')->index('provider_configs_provider');
            $table->string('config_name');
            $table->enum('environment', ['production', 'sandbox', 'testing', 'development'])->nullable()->default('production')->index('provider_configs_environment');
            $table->string('base_url', 500);
            $table->string('api_version', 50)->nullable();
            $table->integer('timeout_seconds')->nullable()->default(30);
            $table->integer('max_retries')->nullable()->default(3);
            $table->integer('retry_delay_seconds')->nullable()->default(5);
            $table->enum('auth_type', ['api_key', 'bearer_token', 'basic', 'oauth2', 'jwt', 'custom'])->nullable()->default('api_key');
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('api_token')->nullable();
            $table->text('public_key')->nullable();
            $table->text('private_key')->nullable();
            $table->timestamp('token_expiry')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->longText('default_headers')->nullable();
            $table->longText('dynamic_headers')->nullable();
            $table->integer('rate_limit_per_minute')->nullable();
            $table->integer('rate_limit_per_hour')->nullable();
            $table->integer('rate_limit_per_day')->nullable();
            $table->boolean('verify_ssl')->nullable()->default(true);
            $table->string('ssl_cert_path', 500)->nullable();
            $table->string('ssl_key_path', 500)->nullable();
            $table->string('health_check_url', 500)->nullable();
            $table->integer('health_check_interval')->nullable()->default(300);
            $table->timestamp('last_health_check')->nullable();
            $table->enum('health_status', ['healthy', 'unhealthy', 'degraded', 'unknown'])->nullable()->default('unknown');
            $table->text('health_message')->nullable();
            $table->integer('avg_response_time')->nullable();
            $table->decimal('success_rate', 5)->nullable();
            $table->bigInteger('total_calls')->nullable()->default(0);
            $table->bigInteger('successful_calls')->nullable()->default(0);
            $table->bigInteger('failed_calls')->nullable()->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_configs');
    }
};

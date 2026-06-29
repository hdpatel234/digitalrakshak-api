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
        Schema::create('ai_api_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('config_name');
            $table->unsignedBigInteger('provider_id')->index('provider_id');
            $table->unsignedBigInteger('model_id')->nullable()->index('model_id');
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('organization_id')->nullable();
            $table->string('project_id')->nullable();
            $table->string('base_url', 500)->nullable();
            $table->string('default_model', 100)->nullable();
            $table->decimal('default_temperature', 3)->nullable()->default(0.7);
            $table->integer('default_max_tokens')->nullable()->default(2048);
            $table->decimal('default_top_p', 3)->nullable()->default(1);
            $table->decimal('default_frequency_penalty', 3)->nullable()->default(0);
            $table->decimal('default_presence_penalty', 3)->nullable()->default(0);
            $table->integer('requests_per_minute')->nullable()->default(60);
            $table->integer('tokens_per_minute')->nullable()->default(90000);
            $table->boolean('enable_streaming')->nullable()->default(true);
            $table->boolean('enable_functions')->nullable()->default(false);
            $table->boolean('enable_vision')->nullable()->default(false);
            $table->enum('environment', ['production', 'sandbox', 'testing'])->nullable()->default('production');
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_default')->nullable()->default(false);
            $table->bigInteger('total_requests')->nullable()->default(0);
            $table->bigInteger('total_tokens')->nullable()->default(0);
            $table->decimal('total_cost', 10, 4)->nullable()->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->enum('health_status', ['healthy', 'unhealthy', 'degraded'])->nullable()->default('healthy');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['is_active', 'environment'], 'ai_api_configs_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_api_configs');
    }
};

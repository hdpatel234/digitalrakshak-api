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
        Schema::create('service_provider_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('provider_id')->index('provider_id');
            $table->unsignedBigInteger('provider_config_id')->nullable()->index('provider_config_id');
            $table->integer('priority')->nullable()->default(0);
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_primary')->nullable()->default(false);
            $table->boolean('is_backup')->nullable()->default(false);
            $table->integer('fallback_threshold')->nullable()->default(3);
            $table->integer('cooldown_period')->nullable()->default(300);
            $table->string('endpoint_override', 500)->nullable();
            $table->string('method_override', 10)->nullable();
            $table->longText('headers_override')->nullable();
            $table->longText('body_template')->nullable();
            $table->enum('current_status', ['active', 'degraded', 'down', 'maintenance'])->nullable()->default('active')->index('service_provider_status');
            $table->integer('failure_count')->nullable()->default(0);
            $table->timestamp('last_failure_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['service_id', 'priority'], 'service_provider_priority');
            $table->unique(['service_id', 'provider_id'], 'unique_service_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_assignments');
    }
};

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
        Schema::create('support_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('support_platform_id')->index('support_platform_id');
            $table->string('config_name', 100);
            $table->boolean('is_default')->nullable()->default(false)->index('client_support_config');
            $table->string('api_url', 500)->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('api_token')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->longText('additional_config')->nullable();
            $table->enum('default_priority', ['low', 'medium', 'high', 'urgent'])->nullable()->default('medium');
            $table->string('default_department', 100)->nullable();
            $table->string('default_assignee')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->nullable()->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_configs');
    }
};

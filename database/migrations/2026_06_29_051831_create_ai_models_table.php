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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id');
            $table->string('model_name');
            $table->string('model_code', 100);
            $table->enum('model_type', ['chat', 'completion', 'embedding', 'image', 'audio'])->nullable()->default('chat');
            $table->text('description')->nullable();
            $table->integer('max_tokens')->nullable();
            $table->integer('max_input_tokens')->nullable();
            $table->integer('max_output_tokens')->nullable();
            $table->boolean('supports_functions')->nullable()->default(false);
            $table->boolean('supports_vision')->nullable()->default(false);
            $table->boolean('supports_streaming')->nullable()->default(true);
            $table->boolean('supports_json_mode')->nullable()->default(false);
            $table->decimal('input_cost_per_1k', 10, 6)->nullable()->default(0);
            $table->decimal('output_cost_per_1k', 10, 6)->nullable()->default(0);
            $table->string('currency', 3)->nullable()->default('USD');
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_default')->nullable()->default(false);
            $table->integer('display_order')->nullable()->default(0);
            $table->longText('capabilities')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['model_type', 'is_active'], 'ai_models_type');
            $table->unique(['provider_id', 'model_code'], 'unique_provider_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};

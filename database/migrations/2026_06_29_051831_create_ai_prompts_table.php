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
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prompt_name');
            $table->string('prompt_code', 100)->unique('prompt_code');
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('system_prompt')->nullable();
            $table->text('user_prompt_template');
            $table->longText('examples')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable()->index('provider_id');
            $table->unsignedBigInteger('model_id')->nullable()->index('model_id');
            $table->decimal('temperature', 3)->nullable();
            $table->integer('max_tokens')->nullable();
            $table->string('response_format')->nullable()->default('text');
            $table->longText('response_schema')->nullable();
            $table->boolean('parse_response')->nullable()->default(false);
            $table->longText('functions')->nullable();
            $table->integer('version')->nullable()->default(1);
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['category', 'is_active'], 'ai_prompts_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};

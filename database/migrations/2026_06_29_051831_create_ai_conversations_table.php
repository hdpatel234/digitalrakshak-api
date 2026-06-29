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
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('conversation_uuid', 100)->unique('conversation_uuid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable()->index('client_id');
            $table->unsignedBigInteger('config_id')->index('config_id');
            $table->unsignedBigInteger('model_id')->index('model_id');
            $table->unsignedBigInteger('prompt_id')->nullable()->index('prompt_id');
            $table->string('title')->nullable();
            $table->longText('context')->nullable();
            $table->integer('total_tokens')->nullable()->default(0);
            $table->decimal('total_cost', 10, 4)->nullable()->default(0);
            $table->enum('status', ['active', 'archived', 'deleted'])->nullable()->default('active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->timestamp('last_message_at')->nullable();

            $table->index(['user_id', 'updated_at'], 'ai_conversations_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};

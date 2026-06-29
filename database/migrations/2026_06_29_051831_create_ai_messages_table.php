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
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversation_id');
            $table->string('message_uuid', 100)->unique('message_uuid');
            $table->enum('role', ['system', 'user', 'assistant', 'function', 'tool'])->index('ai_messages_role');
            $table->longText('content')->nullable();
            $table->string('function_name')->nullable();
            $table->longText('function_arguments')->nullable();
            $table->longText('function_response')->nullable();
            $table->unsignedBigInteger('model_id')->nullable()->index('model_id');
            $table->integer('input_tokens')->nullable()->default(0);
            $table->integer('output_tokens')->nullable()->default(0);
            $table->integer('total_tokens')->nullable()->storedAs('`input_tokens` + `output_tokens`');
            $table->decimal('cost', 10, 4)->nullable()->default(0);
            $table->integer('response_time_ms')->nullable();
            $table->string('finish_reason', 50)->nullable();
            $table->longText('metadata')->nullable();
            $table->longText('raw_request')->nullable();
            $table->longText('raw_response')->nullable();
            $table->boolean('user_rating')->nullable();
            $table->text('user_feedback')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['conversation_id', 'created_at'], 'ai_messages_conversation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};

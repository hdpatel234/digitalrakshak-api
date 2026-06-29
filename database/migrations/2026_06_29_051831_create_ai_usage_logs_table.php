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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('config_id');
            $table->unsignedBigInteger('model_id')->index('model_id');
            $table->unsignedBigInteger('user_id')->nullable()->index('user_id');
            $table->unsignedBigInteger('client_id')->nullable()->index('client_id');
            $table->unsignedBigInteger('conversation_id')->nullable()->index('conversation_id');
            $table->unsignedBigInteger('message_id')->nullable()->index('message_id');
            $table->integer('input_tokens');
            $table->integer('output_tokens');
            $table->integer('total_tokens')->nullable()->storedAs('`input_tokens` + `output_tokens`');
            $table->decimal('cost', 10, 4);
            $table->string('request_type', 100)->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('success')->nullable()->default(true);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('ai_usage_logs_date');

            $table->index(['config_id', 'created_at'], 'ai_usage_logs_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};

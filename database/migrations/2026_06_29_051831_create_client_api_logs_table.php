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
        Schema::create('client_api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('api_key_id')->nullable()->index('api_key_id');
            $table->string('endpoint', 500)->index('api_logs_endpoint');
            $table->string('method', 10);
            $table->longText('request_headers')->nullable();
            $table->longText('request_body')->nullable();
            $table->integer('response_code')->nullable();
            $table->longText('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['success', 'failed', 'rate_limited', 'unauthorized'])->nullable()->default('success')->index('api_logs_status');
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['client_id', 'created_at'], 'api_logs_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_api_logs');
    }
};

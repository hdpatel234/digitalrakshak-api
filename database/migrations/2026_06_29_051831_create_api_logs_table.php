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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id')->nullable()->index('service_id');
            $table->unsignedBigInteger('order_item_id')->nullable()->index('order_item_id');
            $table->string('endpoint', 500);
            $table->string('method', 10);
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
            $table->integer('http_status')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->nullable()->default('pending')->index('idx_api_logs_status');
            $table->text('error_message')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('api_logs_created_at');

            $table->index(['created_at'], 'idx_api_logs_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};

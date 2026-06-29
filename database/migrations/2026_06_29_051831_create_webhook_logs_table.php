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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('source', ['billing', 'support']);
            $table->string('platform', 50);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('event_type', 100);
            $table->longText('payload');
            $table->longText('headers')->nullable();
            $table->boolean('processed')->nullable()->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('webhook_logs_created');

            $table->index(['source', 'processed'], 'webhook_logs_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};

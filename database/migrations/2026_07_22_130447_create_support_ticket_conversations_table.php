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
        Schema::create('support_ticket_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ticket_id')->index('ticket_id');
            $table->string('external_conversation_id')->nullable();
            $table->text('message');
            $table->enum('sender_type', ['client', 'customer', 'agent', 'system'])->nullable()->default('customer');
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->boolean('is_internal')->nullable()->default(false);
            $table->longText('attachments')->nullable();
            $table->longText('conversation_data')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->nullable()->default('pending')->index('ticket_conversations_sync');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_conversations');
    }
};

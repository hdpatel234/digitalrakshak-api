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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->unsignedBigInteger('support_config_id');
            $table->unsignedBigInteger('order_id')->nullable()->index('order_item_id');
            $table->string('external_ticket_id')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('priority_id')->nullable();
            $table->string('ticket_number', 100);
            $table->string('subject', 500);
            $table->text('description');
            $table->string('status')->default('open')->index('tickets_status');
            $table->string('assigned_to')->nullable();
            $table->string('assigned_name')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->longText('ticket_data')->nullable();
            $table->unsignedBigInteger('document_id')->nullable()->index('document_id');
            $table->string('sync_status')->nullable()->default('pending');
            $table->text('sync_message')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->unique(['support_config_id', 'external_ticket_id'], 'external_ticket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};

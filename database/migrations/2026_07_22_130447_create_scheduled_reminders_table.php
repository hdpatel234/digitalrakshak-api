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
        Schema::create('scheduled_reminders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('reminder_type', ['invitation', 'order_status', 'verification_pending', 'payment_due', 'ticket_followup', 'custom']);
            $table->string('reference_type', 50)->comment('candidate, order, ticket, invoice');
            $table->unsignedBigInteger('reference_id');
            $table->string('recipient_email')->index('idx_recipient');
            $table->string('recipient_name')->nullable();
            $table->string('subject', 998);
            $table->longText('body');
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->timestamp('scheduled_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->text('last_error')->nullable();
            $table->json('additional_data')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->softDeletes();

            $table->index(['reference_type', 'reference_id'], 'idx_reference');
            $table->index(['scheduled_at', 'status'], 'idx_scheduled_at_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reminders');
    }
};

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
        Schema::create('email_fetch_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('server_id');
            $table->string('email_uid')->index('idx_email_uid');
            $table->string('from_email');
            $table->string('to_email');
            $table->string('subject', 998);
            $table->longText('body')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('email_date')->nullable()->index('idx_email_date');
            $table->enum('fetch_status', ['pending', 'processed', 'failed', 'ignored'])->default('pending')->index('idx_fetch_status');
            $table->boolean('processed_as_ticket')->default(false);
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('fetch_attempts')->default(1);
            $table->timestamp('last_fetch_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_fetch_logs');
    }
};

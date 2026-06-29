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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('document_config_id')->index('document_config_id');
            $table->unsignedBigInteger('candidate_id')->nullable()->index('documents_candidate');
            $table->unsignedBigInteger('order_id')->nullable()->index('documents_order');
            $table->unsignedBigInteger('order_item_id')->nullable()->index('order_item_id');
            $table->unsignedBigInteger('invitation_id')->nullable()->index('invitation_id');
            $table->unsignedBigInteger('ticket_id')->nullable()->index('ticket_id');
            $table->enum('document_type', ['candidate_photo', 'id_proof', 'address_proof', 'education_certificate', 'verification_report', 'invoice', 'ticket_attachment', 'other']);
            $table->string('document_category', 100)->nullable();
            $table->string('original_filename', 500);
            $table->string('stored_filename', 500);
            $table->string('file_path', 1000);
            $table->integer('file_size');
            $table->string('file_hash')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('extension', 20)->nullable();
            $table->string('external_file_id')->nullable();
            $table->string('external_share_link', 1000)->nullable();
            $table->string('external_share_id')->nullable();
            $table->text('share_password')->nullable();
            $table->timestamp('share_expires_at')->nullable();
            $table->integer('version')->nullable()->default(1);
            $table->boolean('is_encrypted')->nullable()->default(false);
            $table->text('encryption_key')->nullable();
            $table->longText('metadata')->nullable();
            $table->longText('ocr_text')->nullable();
            $table->enum('ocr_status', ['pending', 'processing', 'completed', 'failed'])->nullable()->default('pending');
            $table->timestamp('ocr_completed_at')->nullable();
            $table->string('thumbnail_url', 1000)->nullable();
            $table->string('preview_url', 1000)->nullable();
            $table->integer('download_count')->nullable()->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->unsignedBigInteger('last_downloaded_by')->nullable();
            $table->enum('status', ['active', 'archived', 'deleted'])->nullable()->default('active');
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->nullable()->default('pending')->index('documents_sync');
            $table->text('sync_message')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['client_id', 'document_type'], 'documents_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

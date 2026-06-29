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
        Schema::create('document_ocr_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('document_id')->index('document_id');
            $table->integer('priority')->nullable()->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->nullable()->default('pending')->index('ocr_queue_status');
            $table->integer('attempts')->nullable()->default(0);
            $table->integer('max_attempts')->nullable()->default(3);
            $table->longText('ocr_text')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_ocr_queue');
    }
};

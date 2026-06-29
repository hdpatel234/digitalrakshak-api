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
        Schema::create('generated_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('template_id')->nullable()->index('template_id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->unsignedBigInteger('document_config_id')->index('document_config_id');
            $table->unsignedBigInteger('document_id')->nullable()->index('document_id');
            $table->enum('reference_type', ['order', 'candidate', 'invoice', 'ticket']);
            $table->unsignedBigInteger('reference_id');
            $table->string('document_number', 100)->nullable();
            $table->string('title', 500);
            $table->longText('generated_data')->nullable();
            $table->string('file_path', 1000)->nullable();
            $table->integer('file_size')->nullable();
            $table->timestamp('generated_at')->nullable()->useCurrent();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->integer('download_count')->nullable()->default(0);

            $table->index(['reference_type', 'reference_id'], 'generated_docs_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};

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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('document_id');
            $table->integer('version_number');
            $table->string('stored_filename', 500);
            $table->string('file_path', 1000);
            $table->integer('file_size');
            $table->string('file_hash')->nullable();
            $table->string('external_file_id')->nullable();
            $table->string('change_reason', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['document_id', 'version_number'], 'document_versions_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};

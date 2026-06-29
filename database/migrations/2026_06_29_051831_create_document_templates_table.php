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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('template_name');
            $table->string('template_code', 50)->unique('template_code');
            $table->enum('document_type', ['verification_report', 'invoice', 'certificate', 'consent_form', 'other']);
            $table->string('template_file', 500);
            $table->longText('template_data')->nullable();
            $table->enum('output_format', ['pdf', 'docx', 'html'])->nullable()->default('pdf');
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};

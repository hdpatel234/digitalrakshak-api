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
        Schema::create('user_export_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique('unique_user_export');
            $table->enum('default_format', ['pdf', 'excel', 'csv', 'json'])->nullable()->default('pdf');
            $table->enum('paper_size', ['a4', 'letter', 'legal'])->nullable()->default('a4');
            $table->enum('orientation', ['portrait', 'landscape'])->nullable()->default('portrait');
            $table->boolean('include_timestamps')->nullable()->default(true);
            $table->boolean('include_metadata')->nullable()->default(true);
            $table->enum('compression', ['none', 'zip', 'gzip'])->nullable()->default('none');
            $table->boolean('email_on_complete')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
        Schema::dropIfExists('user_export_preferences');
    }
};

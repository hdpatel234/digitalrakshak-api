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
        Schema::create('document_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('document_platform_id')->index('document_platform_id');
            $table->string('config_name', 100);
            $table->boolean('is_default')->nullable()->default(false)->index('client_document_config');
            $table->string('api_url', 500)->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('root_folder', 500)->nullable();
            $table->string('client_folder', 500)->nullable();
            $table->enum('folder_structure', ['flat', 'client_based', 'date_based', 'hybrid'])->nullable()->default('client_based');
            $table->string('file_naming_convention')->nullable();
            $table->integer('max_file_size')->nullable()->default(10485760);
            $table->longText('allowed_file_types')->nullable();
            $table->boolean('is_public_readable')->nullable()->default(false);
            $table->integer('share_expiry_days')->nullable()->default(7);
            $table->string('webhook_secret')->nullable();
            $table->longText('additional_config')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->nullable()->default('active');
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
        Schema::dropIfExists('document_configs');
    }
};

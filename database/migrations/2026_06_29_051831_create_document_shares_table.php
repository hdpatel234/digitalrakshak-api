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
        Schema::create('document_shares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('document_id')->index('document_id');
            $table->string('share_token')->index('document_shares_token');
            $table->enum('share_type', ['public', 'password_protected', 'internal'])->nullable()->default('public');
            $table->text('password')->nullable();
            $table->timestamp('expires_at')->nullable()->index('document_shares_expires');
            $table->integer('max_downloads')->nullable();
            $table->integer('download_count')->nullable()->default(0);
            $table->string('shared_with_email')->nullable();
            $table->string('shared_with_name')->nullable();
            $table->enum('access_permission', ['view', 'download', 'view_and_download'])->nullable()->default('view');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['share_token'], 'share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_shares');
    }
};

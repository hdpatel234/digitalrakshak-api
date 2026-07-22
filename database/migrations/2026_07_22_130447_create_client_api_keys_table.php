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
        Schema::create('client_api_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->string('key_name');
            $table->string('api_key')->unique('api_key');
            $table->string('api_secret')->nullable();
            $table->enum('key_type', ['production', 'sandbox', 'development'])->nullable()->default('production');
            $table->longText('permissions')->nullable();
            $table->longText('ip_whitelist')->nullable();
            $table->integer('rate_limit')->nullable()->default(60);
            $table->integer('rate_limit_per_day')->nullable()->default(10000);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->bigInteger('total_requests')->nullable()->default(0);
            $table->enum('status', ['active', 'inactive', 'expired', 'revoked'])->nullable()->default('active')->index('client_api_keys_status');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index(['api_key'], 'client_api_keys_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_api_keys');
    }
};

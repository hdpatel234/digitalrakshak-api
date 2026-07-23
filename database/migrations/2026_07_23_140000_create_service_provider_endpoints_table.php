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
        Schema::create('service_provider_endpoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('config_id')->comment('References id in tblservice_provider_configs');
            
            $table->string('api_name', 255)->comment('Human readable name e.g., Phone KYC Non Consent');
            $table->string('api_code', 100)->comment('Unique identifier used in your PHP code');
            
            $table->string('endpoint_path', 500)->comment('Relative path e.g., /api/v1/protean/phones/phone-kyc-non-consent');
            $table->enum('http_method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])->default('POST');
            $table->string('content_type', 100)->default('application/json');
            
            $table->json('custom_headers')->nullable()->comment('Additional headers specific to this endpoint');
            $table->json('request_schema')->nullable()->comment('Expected request payload structure or default values');
            $table->json('response_schema')->nullable()->comment('Expected response structure for parsing mapping');
            
            $table->enum('status', ['active', 'inactive', 'deprecated'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['config_id', 'api_code'], 'idx_config_code');
            $table->foreign('config_id', 'fk_endpoint_config')->references('id')->on('service_provider_configs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_endpoints');
    }
};

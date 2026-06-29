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
        Schema::create('provider_response_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_provider_assignment_id')->index('service_provider_assignment_id');
            $table->string('response_field');
            $table->string('target_field');
            $table->enum('data_type', ['string', 'number', 'boolean', 'date', 'json'])->nullable()->default('string');
            $table->string('path', 500)->nullable();
            $table->string('transform_function', 100)->nullable();
            $table->boolean('is_verification_result')->nullable()->default(false);
            $table->boolean('is_required')->nullable()->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_response_mappings');
    }
};

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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider_name');
            $table->string('provider_code', 100)->unique('provider_code');
            $table->enum('provider_type', ['api', 'webhook', 'manual'])->nullable()->default('api');
            $table->text('description')->nullable();
            $table->string('website', 500)->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone', 50)->nullable();
            $table->string('documentation_url', 500)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'deprecated'])->nullable()->default('active');
            $table->boolean('is_default')->nullable()->default(false);
            $table->integer('priority')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};

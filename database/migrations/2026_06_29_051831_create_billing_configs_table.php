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
        Schema::create('billing_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('billing_platform_id')->index('billing_platform_id');
            $table->string('config_name', 100);
            $table->boolean('is_default')->nullable()->default(false);
            $table->string('api_url', 500)->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('api_token')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->longText('additional_config')->nullable();
            $table->string('invoice_prefix', 50)->nullable();
            $table->string('invoice_series', 50)->nullable();
            $table->decimal('tax_rate', 5)->nullable()->default(0);
            $table->string('currency', 3)->nullable()->default('INR');
            $table->integer('payment_terms_days')->nullable()->default(30);
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
        Schema::dropIfExists('billing_configs');
    }
};

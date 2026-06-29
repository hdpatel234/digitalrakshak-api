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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gateway_name');
            $table->string('gateway_code', 100)->unique('gateway_code');
            $table->string('provider_company')->nullable();
            $table->string('website', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('logo', 500)->nullable();
            $table->longText('supported_methods')->nullable();
            $table->longText('configuration_schema')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_default')->nullable()->default(false);
            $table->integer('display_order')->nullable()->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};

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
        Schema::create('client_payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('method_type_id');
            $table->unsignedBigInteger('gateway_config_id')->nullable();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 500)->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->boolean('is_enabled')->nullable()->default(true);
            $table->boolean('is_default')->nullable()->default(false);
            $table->decimal('min_amount', 10)->nullable();
            $table->decimal('max_amount', 10)->nullable();
            $table->text('instructions')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['client_id', 'method_type_id'], 'unique_client_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_payment_methods');
    }
};

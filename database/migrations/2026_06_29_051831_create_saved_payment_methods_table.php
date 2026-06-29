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
        Schema::create('saved_payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('gateway_config_id');
            $table->unsignedBigInteger('method_type_id');
            $table->string('gateway_customer_id')->nullable();
            $table->string('gateway_payment_method_id')->nullable();
            $table->text('payment_token')->nullable();
            $table->string('display_name')->nullable();
            $table->string('masked_details')->nullable();
            $table->string('expiry_month', 2)->nullable();
            $table->string('expiry_year', 4)->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('card_brand', 50)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('upi_id')->nullable();
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->integer('used_count')->nullable()->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['user_id', 'is_default', 'is_active'], 'saved_payment_methods_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_payment_methods');
    }
};

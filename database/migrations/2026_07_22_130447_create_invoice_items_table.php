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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->index('invoice_id');
            $table->unsignedBigInteger('order_item_id')->nullable()->index('order_item_id');
            $table->string('item_type')->nullable()->default('service');
            $table->string('description', 500);
            $table->integer('quantity')->nullable()->default(1);
            $table->decimal('unit_price', 10);
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->decimal('tax_amount', 10)->nullable()->default(0);
            $table->decimal('tax_percentage', 5)->nullable()->default(0);
            $table->decimal('total_price', 10);
            $table->string('external_item_id')->nullable();
            $table->longText('item_data')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};

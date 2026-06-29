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
        Schema::create('packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('package_name')->nullable();
            $table->string('package_code', 50)->nullable()->unique('package_code');
            $table->text('description')->nullable();
            $table->enum('type', ['admin', 'client'])->nullable()->default('admin');
            $table->integer('client_id')->nullable();
            $table->decimal('total_price', 10)->nullable()->default(0);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 10)->nullable()->default(0);
            $table->decimal('final_price', 10)->nullable()->default(0);
            $table->bigInteger('is_display')->nullable()->default(1);
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('status', 50)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('packages');
    }
};

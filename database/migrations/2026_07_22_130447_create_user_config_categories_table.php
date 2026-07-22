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
        Schema::create('user_config_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category_name', 100);
            $table->string('category_code', 50)->unique('category_code');
            $table->integer('display_order')->nullable()->default(0);
            $table->string('icon', 50)->nullable();
            $table->boolean('is_system')->nullable()->default(false);
            $table->string('status', 252)->nullable()->default('active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
        Schema::dropIfExists('user_config_categories');
    }
};

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
            $table->text('description')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->string('icon', 50)->nullable();
            $table->boolean('is_system')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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

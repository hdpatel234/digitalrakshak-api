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
        Schema::create('user_themes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('theme_name', 100);
            $table->string('theme_code', 50)->unique('theme_code');
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_system')->nullable()->default(false);
            $table->longText('colors')->nullable();
            $table->longText('fonts')->nullable();
            $table->string('border_radius', 10)->nullable()->default('0.5rem');
            $table->string('spacing', 10)->nullable()->default('1rem');
            $table->boolean('animations')->nullable()->default(true);
            $table->string('background_image', 500)->nullable();
            $table->text('custom_css')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_themes');
    }
};

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
        Schema::create('user_accessibility_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique('unique_user_accessibility');
            $table->boolean('high_contrast')->nullable()->default(false);
            $table->boolean('large_text')->nullable()->default(false);
            $table->boolean('reduce_motion')->nullable()->default(false);
            $table->boolean('screen_reader_optimized')->nullable()->default(false);
            $table->boolean('keyboard_navigation')->nullable()->default(true);
            $table->boolean('focus_indicators')->nullable()->default(true);
            $table->enum('color_blind_mode', ['none', 'protanopia', 'deuteranopia', 'tritanopia'])->nullable()->default('none');
            $table->string('font_family', 100)->nullable();
            $table->decimal('font_size_multiplier', 3)->nullable()->default(1);
            $table->decimal('line_height_multiplier', 3)->nullable()->default(1);
            $table->string('letter_spacing', 10)->nullable()->default('normal');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accessibility_settings');
    }
};

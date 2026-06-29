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
        Schema::create('user_config_definitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->index('category_id');
            $table->string('config_key', 100)->unique('unique_config_key');
            $table->string('config_name');
            $table->text('description')->nullable();
            $table->enum('value_type', ['string', 'text', 'integer', 'float', 'boolean', 'json', 'date', 'datetime', 'time', 'email', 'url', 'color', 'select', 'multi_select']);
            $table->text('default_value')->nullable();
            $table->longText('possible_values')->nullable();
            $table->longText('validation_rules')->nullable();
            $table->boolean('is_required')->nullable()->default(false);
            $table->boolean('is_editable')->nullable()->default(true);
            $table->boolean('is_private')->nullable()->default(false);
            $table->integer('display_order')->nullable()->default(0);
            $table->string('ui_component', 100)->nullable();
            $table->longText('ui_props')->nullable();
            $table->longText('depends_on')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_config_definitions');
    }
};

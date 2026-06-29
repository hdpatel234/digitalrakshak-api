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
        Schema::create('user_search_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique('unique_user_search');
            $table->enum('default_search_operator', ['and', 'or'])->nullable()->default('and');
            $table->integer('items_per_page')->nullable()->default(25);
            $table->boolean('save_recent_searches')->nullable()->default(true);
            $table->integer('max_recent_searches')->nullable()->default(10);
            $table->boolean('save_filters')->nullable()->default(true);
            $table->enum('default_date_range', ['today', 'week', 'month', 'quarter', 'year', 'all'])->nullable()->default('month');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_search_preferences');
    }
};

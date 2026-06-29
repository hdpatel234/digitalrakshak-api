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
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('favorite_type', ['candidate', 'order', 'service', 'report', 'page']);
            $table->unsignedBigInteger('favorite_id')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('title');
            $table->string('icon', 50)->nullable();
            $table->longText('metadata')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['user_id', 'favorite_type'], 'user_favorites_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};

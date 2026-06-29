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
        Schema::create('user_recent_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('item_type', ['candidate', 'order', 'service', 'report', 'page']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('title');
            $table->longText('metadata')->nullable();
            $table->timestamp('last_accessed_at')->nullable()->useCurrent();
            $table->integer('access_count')->nullable()->default(1);

            $table->index(['user_id', 'last_accessed_at'], 'user_recent_items_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_recent_items');
    }
};

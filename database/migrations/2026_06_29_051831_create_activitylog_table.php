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
        Schema::create('activitylog', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('description')->nullable();
            $table->integer('user_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('ip_address', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activitylog');
    }
};

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
        Schema::create('api_provider_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('api_provider_id');
            $table->string('endpoint');
            $table->string('method');
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->integer('response_code')->nullable();
            $table->float('duration')->nullable();
            $table->boolean('is_successful')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_provider_logs');
    }
};

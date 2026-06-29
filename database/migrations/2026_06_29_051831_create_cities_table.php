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
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('country_id')->index('cities_country');
            $table->string('name', 100);
            $table->string('local_name', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('postal_code', 20)->nullable()->index('cities_postal');
            $table->longText('postal_codes')->nullable();
            $table->string('timezone', 100)->nullable();
            $table->boolean('is_capital')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true)->index('cities_active');
            $table->integer('display_order')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->unique(['state_id', 'name'], 'city_state_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};

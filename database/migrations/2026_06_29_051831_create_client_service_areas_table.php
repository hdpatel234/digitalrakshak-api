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
        Schema::create('client_service_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('country_id')->nullable()->index('country_id');
            $table->unsignedBigInteger('state_id')->nullable()->index('state_id');
            $table->unsignedBigInteger('city_id')->nullable()->index('city_id');
            $table->enum('service_type', ['all', 'verification', 'support', 'physical'])->nullable()->default('all');
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['client_id', 'country_id', 'state_id', 'city_id'], 'client_service_areas_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_service_areas');
    }
};

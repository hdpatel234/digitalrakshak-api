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
        Schema::create('location_service_pricing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable()->index('client_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('country_id')->nullable()->index('country_id');
            $table->unsignedBigInteger('state_id')->nullable()->index('state_id');
            $table->unsignedBigInteger('city_id')->nullable()->index('city_id');
            $table->enum('price_adjustment_type', ['fixed', 'percentage'])->nullable()->default('fixed');
            $table->decimal('price_adjustment', 10)->nullable()->default(0);
            $table->decimal('final_price', 10)->nullable()->default(0);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['service_id', 'country_id', 'state_id', 'city_id'], 'location_pricing_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_service_pricing');
    }
};

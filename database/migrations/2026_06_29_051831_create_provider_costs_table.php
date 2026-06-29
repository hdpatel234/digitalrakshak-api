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
        Schema::create('provider_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider_id')->index('provider_id');
            $table->unsignedBigInteger('service_id')->nullable()->index('service_id');
            $table->decimal('cost_per_call', 10)->nullable()->default(0);
            $table->string('currency', 3)->nullable()->default('INR');
            $table->enum('billing_model', ['per_call', 'subscription', 'tiered', 'free'])->nullable()->default('per_call');
            $table->integer('minimum_commitment')->nullable();
            $table->enum('commitment_period', ['monthly', 'yearly'])->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_costs');
    }
};

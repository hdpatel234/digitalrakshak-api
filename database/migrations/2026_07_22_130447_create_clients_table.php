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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name');
            $table->string('logo')->nullable();
            $table->string('email')->unique('clients_email_unique');
            $table->string('phone', 50)->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('country_id')->nullable()->index('country_id');
            $table->unsignedBigInteger('state_id')->nullable()->index('state_id');
            $table->unsignedBigInteger('city_id')->nullable()->index('city_id');
            $table->string('pincode', 10)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->nullable()->default('active')->index('clients_status_index');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

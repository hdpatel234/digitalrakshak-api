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
        Schema::create('billing_service_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('billing_platform_id')->index('tblbilling_service_mappings_billing_platform_id_foreign');
            $table->unsignedBigInteger('package_id')->index('tblbilling_service_mappings_package_id_foreign');
            $table->string('external_service_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable()->index('tblbilling_service_mappings_created_by_foreign');
            $table->unsignedBigInteger('updated_by')->nullable()->index('tblbilling_service_mappings_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_service_mappings');
    }
};

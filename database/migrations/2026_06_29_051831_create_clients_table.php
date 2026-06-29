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
            $table->string('contact_person')->nullable();
            $table->string('email')->unique('clients_email_unique');
            $table->string('phone', 20)->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('country_id')->nullable()->index('country_id');
            $table->unsignedBigInteger('state_id')->nullable()->index('state_id');
            $table->unsignedBigInteger('city_id')->nullable()->index('city_id');
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('country', 100)->nullable()->default('India');
            $table->string('currency', 3)->nullable()->default('INR');
            $table->decimal('credit_limit', 10)->nullable()->default(0);
            $table->decimal('credit_balance', 10)->nullable()->default(0);
            $table->integer('payment_terms')->nullable()->default(30);
            $table->unsignedBigInteger('default_billing_config_id')->nullable()->index('tblclients_ibfk_1');
            $table->unsignedBigInteger('default_support_config_id')->nullable()->index('tblclients_ibfk_2');
            $table->unsignedBigInteger('default_document_config_id')->nullable()->index('tblclients_ibfk_3');
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

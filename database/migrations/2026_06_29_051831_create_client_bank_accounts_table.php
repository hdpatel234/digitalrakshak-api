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
        Schema::create('client_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('account_number', 100);
            $table->enum('account_type', ['savings', 'current', 'cash_credit'])->nullable()->default('current');
            $table->string('ifsc_code', 20)->nullable();
            $table->string('swift_code', 20)->nullable();
            $table->string('branch_name')->nullable();
            $table->text('branch_address')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('qr_code', 500)->nullable();
            $table->boolean('is_primary')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);
            $table->integer('display_order')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['client_id', 'is_primary', 'is_active'], 'client_bank_accounts_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_bank_accounts');
    }
};

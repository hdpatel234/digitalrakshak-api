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
        Schema::create('payment_method_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('method_name', 100);
            $table->string('method_code', 50)->unique('method_code');
            $table->enum('category', ['card', 'upi', 'netbanking', 'wallet', 'bank', 'cash', 'other']);
            $table->string('icon', 100)->nullable();
            $table->text('description')->nullable();
            $table->longText('configuration_schema')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->integer('display_order')->nullable()->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_types');
    }
};

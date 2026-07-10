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
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            $table->renameColumn('is_active', 'status');
        });

        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            $table->renameColumn('status', 'is_active');
        });

        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(1)->change();
        });
    }
};

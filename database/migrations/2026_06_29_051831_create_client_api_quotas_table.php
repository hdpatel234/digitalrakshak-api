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
        Schema::create('client_api_quotas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->date('period_start');
            $table->date('period_end')->index('api_quotas_period');
            $table->integer('requests_limit');
            $table->integer('requests_used')->nullable()->default(0);
            $table->integer('requests_remaining')->nullable()->storedAs('`requests_limit` - `requests_used`');
            $table->timestamp('reset_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['client_id', 'period_start'], 'client_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_api_quotas');
    }
};

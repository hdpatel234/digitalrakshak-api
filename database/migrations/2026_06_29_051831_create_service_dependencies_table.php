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
        Schema::create('service_dependencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('depends_on_service_id')->index('depends_on_service_id');
            $table->enum('dependency_type', ['required', 'optional', 'sequential'])->nullable()->default('required');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['service_id', 'depends_on_service_id'], 'unique_service_dependency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_dependencies');
    }
};

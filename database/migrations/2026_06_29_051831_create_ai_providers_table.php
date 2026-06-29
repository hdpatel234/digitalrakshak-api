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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider_name', 100);
            $table->string('provider_code', 50)->unique('provider_code');
            $table->enum('provider_type', ['chat', 'completion', 'embedding', 'image'])->nullable()->default('chat');
            $table->text('description')->nullable();
            $table->string('website', 500)->nullable();
            $table->string('documentation_url', 500)->nullable();
            $table->string('icon', 100)->nullable();
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
        Schema::dropIfExists('ai_providers');
    }
};

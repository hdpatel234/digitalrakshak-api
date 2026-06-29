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
        Schema::create('ai_prompt_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prompt_id');
            $table->integer('version');
            $table->text('system_prompt')->nullable();
            $table->text('user_prompt_template');
            $table->longText('examples')->nullable();
            $table->decimal('temperature', 3)->nullable();
            $table->integer('max_tokens')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['prompt_id', 'version'], 'unique_prompt_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompt_versions');
    }
};

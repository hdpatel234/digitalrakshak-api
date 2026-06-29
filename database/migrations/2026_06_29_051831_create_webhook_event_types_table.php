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
        Schema::create('webhook_event_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_name', 100);
            $table->string('event_code', 100)->unique('event_code');
            $table->string('category', 50);
            $table->text('description')->nullable();
            $table->longText('sample_payload')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_event_types');
    }
};

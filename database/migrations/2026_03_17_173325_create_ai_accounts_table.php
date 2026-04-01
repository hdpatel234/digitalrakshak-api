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
        Schema::create('ai_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->index();
            $table->string('api_key', 255)->unique();
            $table->integer('daily_usage')->default(0);
            $table->integer('limit_per_day')->default(20);
            $table->date('last_used_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('provider_id')
                ->references('id')
                ->on('ai_providers')
                ->cascadeOnDelete();
        });

        // Insert dummy Gemini accounts
        // We assume provider_id = 1 is Gemini (this will be updated manually if different or the user can match the correct ID)
        DB::table('ai_accounts')->insert([
            ['provider_id' => 1, 'api_key' => 'dummy_gemini_key_1', 'daily_usage' => 0, 'limit_per_day' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['provider_id' => 1, 'api_key' => 'dummy_gemini_key_2', 'daily_usage' => 0, 'limit_per_day' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['provider_id' => 1, 'api_key' => 'dummy_gemini_key_3', 'daily_usage' => 0, 'limit_per_day' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['provider_id' => 1, 'api_key' => 'dummy_gemini_key_4', 'daily_usage' => 0, 'limit_per_day' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['provider_id' => 1, 'api_key' => 'dummy_gemini_key_5', 'daily_usage' => 0, 'limit_per_day' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_accounts');
    }
};

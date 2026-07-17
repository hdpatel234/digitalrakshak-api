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
        Schema::table('services_fields', function (Blueprint $table) {
            $table->json('field_options')->nullable()->comment('For select, radio, checkbox options');
            $table->text('default_value')->nullable();
            $table->string('placeholder', 255)->nullable();
            $table->text('help_text')->nullable();
            $table->integer('min_length')->nullable();
            $table->integer('max_length')->nullable();
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('max_value', 10, 2)->nullable();
            $table->boolean('is_encrypted')->default(0);
            $table->boolean('is_verifiable')->default(1)->comment('Whether this field needs verification');
            $table->unsignedBigInteger('verification_provider_id')->nullable()->comment('Provider to verify this field');
            $table->string('verification_rule', 255)->nullable()->comment('Verification rule/endpoint');
            $table->integer('verification_timeout')->default(30)->comment('Timeout in seconds');
            $table->boolean('is_readonly_after_verification')->default(0);
            $table->json('dependencies')->nullable()->comment('Conditional dependencies on other fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services_fields', function (Blueprint $table) {
            $table->dropColumn([
                'field_options',
                'default_value',
                'placeholder',
                'help_text',
                'min_length',
                'max_length',
                'min_value',
                'max_value',
                'is_encrypted',
                'is_verifiable',
                'verification_provider_id',
                'verification_rule',
                'verification_timeout',
                'is_readonly_after_verification',
                'dependencies'
            ]);
        });
    }
};

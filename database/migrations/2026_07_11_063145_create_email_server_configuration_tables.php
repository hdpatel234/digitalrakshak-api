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
        Schema::create('email_server_configuration_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_type_id');
            $table->string('field_name', 100);
            $table->string('field_label', 150);
            $table->enum('field_type', [
                'text',
                'password',
                'number',
                'email',
                'url',
                'select',
                'checkbox',
                'textarea'
            ])->default('text');
            $table->boolean('is_required')->default(true);
            $table->text('default_value')->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('help_text')->nullable();
            $table->string('placeholder', 255)->nullable();
            $table->string('validation_rules', 255)->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
        });

        Schema::create('email_server_configuration_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_server_id');
            $table->unsignedBigInteger('configuration_field_id');
            $table->longText('field_value')->nullable();
            $table->timestamps();

            $table->unique(['email_server_id', 'configuration_field_id'], 'email_server_config_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_server_configuration_values');
        Schema::dropIfExists('email_server_configuration_fields');
    }
};

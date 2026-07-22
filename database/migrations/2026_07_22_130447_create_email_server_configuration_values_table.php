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
        Schema::create('email_server_configuration_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_server_id');
            $table->unsignedBigInteger('configuration_field_id');
            $table->longText('field_value')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique(['email_server_id', 'configuration_field_id'], 'email_server_config_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_server_configuration_values');
    }
};

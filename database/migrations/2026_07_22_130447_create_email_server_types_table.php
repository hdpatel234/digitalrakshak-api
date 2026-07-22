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
        Schema::create('email_server_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type_name', 100);
            $table->string('type_code', 50)->unique('type_code');
            $table->text('description')->nullable();
            $table->boolean('is_outgoing')->nullable()->default(true);
            $table->boolean('is_incoming')->nullable()->default(false);
            $table->string('status')->nullable()->default('active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_server_types');
    }
};

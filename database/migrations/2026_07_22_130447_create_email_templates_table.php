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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('server_id')->default(0);
            $table->string('template_name');
            $table->string('template_code', 100)->unique('template_code');
            $table->string('email_type', 100)->nullable()->index('email_templates_type');
            $table->string('subject', 998);
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->longText('variables')->nullable();
            $table->enum('default_priority', ['high', 'normal', 'low'])->nullable()->default('normal');
            $table->longText('allowed_attachments')->nullable();
            $table->string('status')->nullable()->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

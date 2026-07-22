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
        Schema::create('email_to_ticket_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rule_name');
            $table->integer('rule_priority')->default(0)->index('idx_priority');
            $table->string('match_type')->default('\'all\'');
            $table->string('match_value', 500)->nullable();
            $table->string('match_pattern', 500)->nullable();
            $table->unsignedBigInteger('ticket_department_id')->nullable();
            $table->unsignedBigInteger('ticket_priority_id')->nullable();
            $table->string('ticket_category', 100)->nullable();
            $table->unsignedBigInteger('auto_assign_user_id')->nullable();
            $table->unsignedBigInteger('auto_response_template_id')->nullable();
            $table->boolean('create_ticket')->default(true);
            $table->boolean('send_auto_response')->default(false);
            $table->string('ticket_subject_prefix', 100)->nullable();
            $table->string('ticket_subject_suffix', 100)->nullable();
            $table->string('customer_email_field', 100)->default('\'from_email\'');
            $table->string('customer_name_field', 100)->default('\'from_name\'');
            $table->integer('escalate_after_hours')->nullable();
            $table->unsignedBigInteger('escalate_user_id')->nullable();
            $table->longText('additional_config')->nullable();
            $table->string('status')->default('active')->index('idx_active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_to_ticket_rules');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // 1. Add missing audit fields to email tables
        if (Schema::hasTable('email_attachments')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                if (!Schema::hasColumn('email_attachments', 'created_by')) $table->unsignedBigInteger('created_by')->nullable();
                if (!Schema::hasColumn('email_attachments', 'updated_by')) $table->unsignedBigInteger('updated_by')->nullable();
                if (!Schema::hasColumn('email_attachments', 'deleted_by')) $table->unsignedBigInteger('deleted_by')->nullable();
                if (!Schema::hasColumn('email_attachments', 'deleted_at')) $table->timestamp('deleted_at')->nullable();
            });
        }

        if (Schema::hasTable('email_bounces')) {
            Schema::table('email_bounces', function (Blueprint $table) {
                if (!Schema::hasColumn('email_bounces', 'created_by')) $table->unsignedBigInteger('created_by')->nullable();
                if (!Schema::hasColumn('email_bounces', 'updated_by')) $table->unsignedBigInteger('updated_by')->nullable();
                if (!Schema::hasColumn('email_bounces', 'deleted_by')) $table->unsignedBigInteger('deleted_by')->nullable();
                if (!Schema::hasColumn('email_bounces', 'deleted_at')) $table->timestamp('deleted_at')->nullable();
            });
        }

        // 2. Enhance email_queue for better cron processing
        if (Schema::hasTable('email_queue')) {
            Schema::table('email_queue', function (Blueprint $table) {
                if (!Schema::hasColumn('email_queue', 'scheduled_at')) $table->timestamp('scheduled_at')->nullable()->after('last_attempt_at');
                if (!Schema::hasColumn('email_queue', 'expires_at')) $table->timestamp('expires_at')->nullable()->after('scheduled_at');
                if (!Schema::hasColumn('email_queue', 'batch_id')) $table->string('batch_id', 100)->nullable();
                if (!Schema::hasColumn('email_queue', 'parent_email_id')) $table->unsignedBigInteger('parent_email_id')->nullable()->comment('For follow-up emails');
                if (!Schema::hasColumn('email_queue', 'email_category')) $table->enum('email_category', ['invitation', 'order_confirmation', 'status_update', 'support_ticket', 'welcome', 'credentials', 'password_reset', 'reminder', 'report', 'notification'])->default('notification');
                if (!Schema::hasColumn('email_queue', 'retry_count')) $table->integer('retry_count')->default(0);
                if (!Schema::hasColumn('email_queue', 'max_retry_count')) $table->integer('max_retry_count')->default(3);
                if (!Schema::hasColumn('email_queue', 'last_error_code')) $table->string('last_error_code', 50)->nullable();
                if (!Schema::hasColumn('email_queue', 'processed_by')) $table->unsignedBigInteger('processed_by')->nullable();
                if (!Schema::hasColumn('email_queue', 'processed_at')) $table->timestamp('processed_at')->nullable();
                if (!Schema::hasColumn('email_queue', 'created_by')) $table->unsignedBigInteger('created_by')->nullable();
                if (!Schema::hasColumn('email_queue', 'updated_by')) $table->unsignedBigInteger('updated_by')->nullable();
                if (!Schema::hasColumn('email_queue', 'deleted_by')) $table->unsignedBigInteger('deleted_by')->nullable();
                if (!Schema::hasColumn('email_queue', 'deleted_at')) $table->timestamp('deleted_at')->nullable();

                if (!Schema::hasIndex('email_queue', 'idx_status_scheduled')) $table->index(['status', 'scheduled_at'], 'idx_status_scheduled');
                if (!Schema::hasIndex('email_queue', 'idx_batch_id')) $table->index('batch_id', 'idx_batch_id');
            });
        }

        // 3. Create email_fetch_logs table for incoming email tracking
        if (!Schema::hasTable('email_fetch_logs')) {
            Schema::create('email_fetch_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('server_id');
                $table->string('email_uid', 255);
                $table->string('from_email', 255);
                $table->string('to_email', 255);
                $table->string('subject', 998);
                $table->longText('body')->nullable();
                $table->longText('body_html')->nullable();
                $table->json('attachments')->nullable();
                $table->timestamp('email_date')->nullable();
                $table->enum('fetch_status', ['pending', 'processed', 'failed', 'ignored'])->default('pending');
                $table->boolean('processed_as_ticket')->default(0);
                $table->unsignedBigInteger('ticket_id')->nullable();
                $table->text('error_message')->nullable();
                $table->integer('fetch_attempts')->default(1);
                $table->timestamp('last_fetch_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                if (Schema::hasTable('email_servers')) $table->foreign('server_id')->references('id')->on('email_servers');
                if (Schema::hasTable('support_tickets')) $table->foreign('ticket_id')->references('id')->on('support_tickets');

                $table->index('email_uid', 'idx_email_uid');
                $table->index('fetch_status', 'idx_fetch_status');
                $table->index('email_date', 'idx_email_date');
            });
        }

        // 4. Create email_to_ticket_rules table
        if (!Schema::hasTable('email_to_ticket_rules')) {
            Schema::create('email_to_ticket_rules', function (Blueprint $table) {
                $table->id();
                $table->string('rule_name', 255);
                $table->integer('rule_priority')->default(0);
                $table->boolean('is_active')->default(1);
                $table->enum('match_type', ['from_domain', 'from_email', 'subject_contains', 'body_contains', 'to_email', 'all'])->default('all');
                $table->string('match_value', 500)->nullable();
                $table->string('match_pattern', 500)->nullable();
                $table->unsignedBigInteger('ticket_department_id')->nullable();
                $table->unsignedBigInteger('ticket_priority_id')->nullable();
                $table->string('ticket_category', 100)->nullable();
                $table->unsignedBigInteger('auto_assign_user_id')->nullable();
                $table->unsignedBigInteger('auto_response_template_id')->nullable();
                $table->boolean('create_ticket')->default(1);
                $table->boolean('send_auto_response')->default(0);
                $table->string('ticket_subject_prefix', 100)->nullable();
                $table->string('ticket_subject_suffix', 100)->nullable();
                $table->string('customer_email_field', 100)->default('from_email');
                $table->string('customer_name_field', 100)->default('from_name');
                $table->integer('escalate_after_hours')->nullable();
                $table->unsignedBigInteger('escalate_user_id')->nullable();
                $table->json('additional_config')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                if (Schema::hasTable('support_departments')) $table->foreign('ticket_department_id')->references('id')->on('support_departments');
                if (Schema::hasTable('support_priorities')) $table->foreign('ticket_priority_id')->references('id')->on('support_priorities');
                if (Schema::hasTable('users')) $table->foreign('auto_assign_user_id')->references('id')->on('users');

                $table->index('rule_priority', 'idx_priority');
                $table->index('is_active', 'idx_active');
            });
        }

        // 5. Create cron_job_logs table for tracking cron executions
        if (!Schema::hasTable('cron_job_logs')) {
            Schema::create('cron_job_logs', function (Blueprint $table) {
                $table->id();
                $table->string('job_name', 255);
                $table->enum('job_type', ['email_send', 'email_fetch', 'candidate_import', 'order_processing', 'verification_processing', 'report_generation', 'cleanup', 'scheduled_reminder', 'system_maintenance']);
                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('completed_at')->nullable();
                $table->integer('duration_seconds')->nullable();
                $table->enum('status', ['running', 'completed', 'failed', 'cancelled'])->default('running');
                $table->integer('processed_count')->default(0);
                $table->integer('success_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->text('error_message')->nullable();
                $table->json('error_details')->nullable();
                $table->enum('trigger_type', ['manual', 'scheduled', 'webhook', 'system'])->default('scheduled');
                $table->unsignedBigInteger('triggered_by')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->bigInteger('memory_usage')->nullable()->comment('Memory used in bytes');
                $table->decimal('cpu_usage', 5, 2)->nullable()->comment('CPU usage percentage');
                $table->json('details')->nullable()->comment('Additional job details');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index('job_name', 'idx_job_name');
                $table->index('status', 'idx_status');
                $table->index('started_at', 'idx_started_at');
            });
        }

        // 6. Create cron_job_schedules table for configurable schedules
        if (!Schema::hasTable('cron_job_schedules')) {
            Schema::create('cron_job_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('job_name', 255);
                $table->enum('job_type', ['email_send', 'email_fetch', 'candidate_import', 'order_processing', 'verification_processing', 'report_generation', 'cleanup', 'scheduled_reminder', 'system_maintenance']);
                $table->enum('schedule_type', ['cron', 'interval', 'daily', 'weekly', 'monthly', 'custom'])->default('cron');
                $table->string('cron_expression', 100)->nullable();
                $table->integer('interval_seconds')->nullable();
                $table->time('time_of_day')->nullable();
                $table->string('day_of_week', 20)->nullable();
                $table->integer('day_of_month')->nullable();
                $table->integer('month')->nullable();
                $table->string('timezone', 100)->default('UTC');
                $table->boolean('is_active')->default(1);
                $table->integer('max_runtime_seconds')->default(3600);
                $table->integer('max_retries')->default(3);
                $table->integer('retry_delay_seconds')->default(60);
                $table->boolean('concurrent_instances')->default(0)->comment('Allow multiple instances to run simultaneously');
                $table->timestamp('last_run_at')->nullable();
                $table->timestamp('next_run_at')->nullable();
                $table->enum('last_run_status', ['success', 'failed', 'partial'])->nullable();
                $table->unsignedBigInteger('last_run_log_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index('job_name', 'idx_job_name');
                $table->index(['is_active', 'next_run_at'], 'idx_active_next_run');
                $table->index('last_run_at', 'idx_last_run');
            });
        }

        // 7. Create candidate_import_queue table for batch processing
        if (!Schema::hasTable('candidate_import_queue')) {
            Schema::create('candidate_import_queue', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->string('file_name', 255);
                $table->string('file_path', 1000);
                $table->bigInteger('file_size')->nullable();
                $table->string('file_hash', 255)->nullable();
                $table->string('sheet_name', 100)->nullable();
                $table->integer('total_rows')->default(0);
                $table->integer('processed_rows')->default(0);
                $table->integer('successful_rows')->default(0);
                $table->integer('failed_rows')->default(0);
                $table->boolean('skip_existing')->default(0);
                $table->boolean('update_existing')->default(0);
                $table->boolean('send_invitations')->default(0);
                $table->unsignedBigInteger('invitation_template_id')->nullable();
                $table->json('field_mapping')->comment('Mapping of CSV columns to candidate fields');
                $table->json('default_values')->nullable()->comment('Default values for missing fields');
                $table->integer('batch_size')->default(100);
                $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'paused', 'cancelled'])->default('pending');
                $table->integer('progress_percentage')->default(0);
                $table->timestamp('processed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('error_summary')->nullable();
                $table->json('error_details')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                if (Schema::hasTable('clients')) $table->foreign('client_id')->references('id')->on('clients');
                if (Schema::hasTable('email_templates')) $table->foreign('invitation_template_id')->references('id')->on('email_templates');

                $table->index('status', 'idx_status');
                $table->index('created_at', 'idx_created_at');
            });
        }

        // 8. Enhance tblcandidates for import tracking
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                if (!Schema::hasColumn('candidates', 'import_id')) $table->unsignedBigInteger('import_id')->nullable()->comment('Reference to import batch');
                if (!Schema::hasColumn('candidates', 'import_row_number')) $table->integer('import_row_number')->nullable();
                if (!Schema::hasColumn('candidates', 'is_imported')) $table->boolean('is_imported')->default(0);
                if (!Schema::hasColumn('candidates', 'import_status')) $table->enum('import_status', ['pending', 'imported', 'failed', 'skipped'])->nullable();
                if (!Schema::hasColumn('candidates', 'import_error')) $table->text('import_error')->nullable();

                if (!Schema::hasIndex('candidates', 'idx_import_id')) $table->index('import_id', 'idx_import_id');
            });
        }

        // 9. Create email_status_history table
        if (!Schema::hasTable('email_status_history')) {
            Schema::create('email_status_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('email_queue_id');
                $table->string('old_status', 50)->nullable();
                $table->string('new_status', 50);
                $table->text('reason')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                if (Schema::hasTable('email_queue')) $table->foreign('email_queue_id')->references('id')->on('email_queue');

                $table->index('email_queue_id', 'idx_email_queue');
                $table->index('new_status', 'idx_new_status');
                $table->index('created_at', 'idx_created_at');
            });
        }

        // 10. Create scheduled_reminders table
        if (!Schema::hasTable('scheduled_reminders')) {
            Schema::create('scheduled_reminders', function (Blueprint $table) {
                $table->id();
                $table->enum('reminder_type', ['invitation', 'order_status', 'verification_pending', 'payment_due', 'ticket_followup', 'custom']);
                $table->string('reference_type', 50)->comment('candidate, order, ticket, invoice');
                $table->unsignedBigInteger('reference_id');
                $table->string('recipient_email', 255);
                $table->string('recipient_name', 255)->nullable();
                $table->string('subject', 998);
                $table->longText('body');
                $table->unsignedBigInteger('email_template_id')->nullable();
                $table->timestamp('scheduled_at');
                $table->timestamp('sent_at')->nullable();
                $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
                $table->integer('retry_count')->default(0);
                $table->integer('max_retries')->default(3);
                $table->text('last_error')->nullable();
                $table->json('additional_data')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('deleted_at')->nullable();

                $table->index(['scheduled_at', 'status'], 'idx_scheduled_at_status');
                $table->index(['reference_type', 'reference_id'], 'idx_reference');
                $table->index('recipient_email', 'idx_recipient');
            });
        }

        // 11. Add ticket_email_reference to support_tickets
        if (Schema::hasTable('support_tickets')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                if (!Schema::hasColumn('support_tickets', 'email_fetch_log_id')) $table->unsignedBigInteger('email_fetch_log_id')->nullable();
                if (!Schema::hasColumn('support_tickets', 'email_message_id')) $table->string('email_message_id', 255)->nullable();
                if (!Schema::hasColumn('support_tickets', 'is_from_email')) $table->boolean('is_from_email')->default(0);
                if (!Schema::hasColumn('support_tickets', 'replied_to_email')) $table->boolean('replied_to_email')->default(0);
                if (!Schema::hasColumn('support_tickets', 'last_email_received_at')) $table->timestamp('last_email_received_at')->nullable();
                if (!Schema::hasColumn('support_tickets', 'last_email_sent_at')) $table->timestamp('last_email_sent_at')->nullable();

                if (!Schema::hasIndex('support_tickets', 'idx_email_fetch')) $table->index('email_fetch_log_id', 'idx_email_fetch');
                if (!Schema::hasIndex('support_tickets', 'idx_email_message')) $table->index('email_message_id', 'idx_email_message');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_reminders');
        Schema::dropIfExists('email_status_history');
        Schema::dropIfExists('candidate_import_queue');
        Schema::dropIfExists('cron_job_schedules');
        Schema::dropIfExists('cron_job_logs');
        Schema::dropIfExists('email_to_ticket_rules');
        Schema::dropIfExists('email_fetch_logs');
    }
};

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
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn([
                'email_uid',
                'to_email',
                'subject',
                'message_id',
                'opens',
                'clicks',
                'sent_at',
                'opened_at',
                'clicked_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->string('email_uid', 100)->nullable();
            $table->string('to_email', 500)->nullable();
            $table->string('subject', 998)->nullable();
            $table->string('message_id', 255)->nullable();
            $table->integer('opens')->default(0);
            $table->integer('clicks')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
        });
    }
};

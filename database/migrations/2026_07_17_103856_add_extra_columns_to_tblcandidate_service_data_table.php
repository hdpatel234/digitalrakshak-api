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
        Schema::table('candidate_service_data', function (Blueprint $table) {
            $table->binary('encrypted_value')->nullable()->comment('Encrypted field value');
            $table->string('encryption_key_id', 100)->nullable()->comment('Reference to encryption key');
            $table->boolean('is_encrypted')->default(0);
            $table->enum('verification_status', ['pending', 'in_progress', 'verified', 'failed', 'not_required'])->default('pending');
            $table->unsignedBigInteger('verification_result_id')->nullable();
            $table->integer('verification_attempts')->default(0);
            $table->text('last_verification_error')->nullable();
            $table->boolean('consent_given')->default(0);
            $table->timestamp('consent_timestamp')->nullable();
            $table->string('consent_method', 50)->nullable()->comment('email, form, manual');
            $table->string('data_source', 50)->default('user_input')->comment('user_input, import, api, manual');
            $table->string('source_reference', 255)->nullable()->comment('Reference to source system');
            $table->string('data_hash', 255)->nullable()->comment('Hash for data integrity');
            $table->integer('version')->default(1);
            
            $table->foreign('verification_result_id')->references('id')->on('verification_results');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_service_data', function (Blueprint $table) {
            $table->dropForeign(['verification_result_id']);
            $table->dropColumn([
                'encrypted_value',
                'encryption_key_id',
                'is_encrypted',
                'verification_status',
                'verification_result_id',
                'verification_attempts',
                'last_verification_error',
                'consent_given',
                'consent_timestamp',
                'consent_method',
                'data_source',
                'source_reference',
                'data_hash',
                'version'
            ]);
        });
    }
};

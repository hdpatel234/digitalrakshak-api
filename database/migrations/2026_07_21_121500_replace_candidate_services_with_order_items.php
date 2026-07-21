<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter candidate_service_data table to point to order_items instead of candidate_services
        if (Schema::hasTable('candidate_service_data')) {
            Schema::table('candidate_service_data', function (Blueprint $table) {
                // We'll just drop the old column and add the new one, since foreign keys might be attached
                // Assuming we don't need to migrate data directly or we could rename column
                if (Schema::hasColumn('candidate_service_data', 'candidate_service_id')) {
                    // Try renaming the column if it's simpler
                    $table->renameColumn('candidate_service_id', 'order_item_id');
                } else if (!Schema::hasColumn('candidate_service_data', 'order_item_id')) {
                    $table->integer('order_item_id')->after('id')->index();
                }
            });
        }

        // Alter candidate_service_logs table if it exists
        if (Schema::hasTable('candidate_service_logs')) {
            Schema::table('candidate_service_logs', function (Blueprint $table) {
                if (Schema::hasColumn('candidate_service_logs', 'candidate_service_id')) {
                    $table->renameColumn('candidate_service_id', 'order_item_id');
                } else if (!Schema::hasColumn('candidate_service_logs', 'order_item_id')) {
                    $table->integer('order_item_id')->after('id')->index();
                }
            });
        }

        // Alter employment_verifications table if it exists
        if (Schema::hasTable('employment_verifications')) {
            Schema::table('employment_verifications', function (Blueprint $table) {
                // Drop the foreign key constraint
                $table->dropForeign(['candidate_service_id']);
                
                if (Schema::hasColumn('employment_verifications', 'candidate_service_id')) {
                    $table->renameColumn('candidate_service_id', 'order_item_id');
                } else if (!Schema::hasColumn('employment_verifications', 'order_item_id')) {
                    $table->unsignedBigInteger('order_item_id')->after('id')->index();
                }

                // Add the new foreign key
                $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            });
        }

        // Alter verification_reports table if it exists
        if (Schema::hasTable('verification_reports')) {
            Schema::table('verification_reports', function (Blueprint $table) {
                // Drop the foreign key constraint
                $table->dropForeign(['candidate_service_id']);
                
                if (Schema::hasColumn('verification_reports', 'candidate_service_id')) {
                    $table->renameColumn('candidate_service_id', 'order_item_id');
                } else if (!Schema::hasColumn('verification_reports', 'order_item_id')) {
                    $table->unsignedBigInteger('order_item_id')->after('id')->index();
                }

                // Add the new foreign key
                $table->foreign('order_item_id')->references('id')->on('order_items');
            });
        }

        // Finally, drop the legacy candidate_services table
        Schema::dropIfExists('candidate_services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To reverse, we'd recreate the candidate_services table and rename columns back
        Schema::create('candidate_services', function (Blueprint $table) {
            $table->id();
            $table->integer('candidate_id')->index('idx_candidate_services_candidate');
            $table->integer('service_id')->index('idx_candidate_services_service');
            $table->string('status', 50)->default('pending');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::hasTable('candidate_service_data')) {
            Schema::table('candidate_service_data', function (Blueprint $table) {
                if (Schema::hasColumn('candidate_service_data', 'order_item_id')) {
                    $table->renameColumn('order_item_id', 'candidate_service_id');
                }
            });
        }

        if (Schema::hasTable('candidate_service_logs')) {
            Schema::table('candidate_service_logs', function (Blueprint $table) {
                if (Schema::hasColumn('candidate_service_logs', 'order_item_id')) {
                    $table->renameColumn('order_item_id', 'candidate_service_id');
                }
            });
        }

        if (Schema::hasTable('employment_verifications')) {
            Schema::table('employment_verifications', function (Blueprint $table) {
                $table->dropForeign(['order_item_id']);
                if (Schema::hasColumn('employment_verifications', 'order_item_id')) {
                    $table->renameColumn('order_item_id', 'candidate_service_id');
                }
                $table->foreign('candidate_service_id')->references('id')->on('candidate_services')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('verification_reports')) {
            Schema::table('verification_reports', function (Blueprint $table) {
                $table->dropForeign(['order_item_id']);
                if (Schema::hasColumn('verification_reports', 'order_item_id')) {
                    $table->renameColumn('order_item_id', 'candidate_service_id');
                }
                $table->foreign('candidate_service_id')->references('id')->on('candidate_services');
            });
        }
    }
};

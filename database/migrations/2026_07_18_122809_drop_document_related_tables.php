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
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('documents');
        Schema::dropIfExists('client_document_configs');
        Schema::dropIfExists('document_ocr_queue');
        Schema::dropIfExists('document_platforms');
        Schema::dropIfExists('document_shares');
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('document_versions');
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not adding recreate logic as it would be too large and we just drop them.
    }
};

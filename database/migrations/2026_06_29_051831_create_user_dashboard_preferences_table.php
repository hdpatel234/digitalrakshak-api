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
        Schema::create('user_dashboard_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique('unique_user_dashboard');
            $table->string('default_dashboard', 100)->nullable()->default('main');
            $table->longText('widget_layout')->nullable();
            $table->longText('hidden_widgets')->nullable();
            $table->longText('widget_settings')->nullable();
            $table->integer('refresh_interval')->nullable()->default(300);
            $table->enum('default_view', ['list', 'grid', 'calendar', 'timeline'])->nullable()->default('list');
            $table->integer('items_per_page')->nullable()->default(25);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_preferences');
    }
};

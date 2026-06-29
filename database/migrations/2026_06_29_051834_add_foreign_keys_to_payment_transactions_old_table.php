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
        Schema::table('payment_transactions_old', function (Blueprint $table) {
            $table->foreign(['client_id'], 'tblpayment_transactions_old_ibfk_1')->references(['id'])->on('clients')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['invoice_id'], 'tblpayment_transactions_old_ibfk_2')->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['order_id'], 'tblpayment_transactions_old_ibfk_3')->references(['id'])->on('candidate_orders')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['billing_config_id'], 'tblpayment_transactions_old_ibfk_4')->references(['id'])->on('billing_configs')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions_old', function (Blueprint $table) {
            $table->dropForeign('tblpayment_transactions_old_ibfk_1');
            $table->dropForeign('tblpayment_transactions_old_ibfk_2');
            $table->dropForeign('tblpayment_transactions_old_ibfk_3');
            $table->dropForeign('tblpayment_transactions_old_ibfk_4');
        });
    }
};

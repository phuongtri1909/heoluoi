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
        Schema::table('bank_auto_deposits', function (Blueprint $table) {
            $table->string('casso_transaction_id')->nullable()->after('transaction_code')->comment('ID giao dịch từ Casso');
            $table->index('casso_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_auto_deposits', function (Blueprint $table) {
            $table->dropIndex(['casso_transaction_id']);
            $table->dropColumn('casso_transaction_id');
        });
    }
};
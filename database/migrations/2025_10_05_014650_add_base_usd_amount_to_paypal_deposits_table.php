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
        Schema::table('paypal_deposits', function (Blueprint $table) {
            $table->decimal('base_usd_amount', 8, 2)->default(0)->after('request_payment_paypal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paypal_deposits', function (Blueprint $table) {
            $table->dropColumn('base_usd_amount');
        });
    }
};
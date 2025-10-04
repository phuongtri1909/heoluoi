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
        Schema::table('request_payment_paypals', function (Blueprint $table) {
            // Add missing columns that PaypalDepositController is trying to insert
            $table->enum('payment_method', ['friends_family', 'goods_services'])->default('friends_family')->after('base_usd_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_payment_paypals', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
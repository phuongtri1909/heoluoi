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
            $table->decimal('base_usd_amount', 8, 2)->default(0)->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_payment_paypals', function (Blueprint $table) {
            $table->dropColumn('base_usd_amount');
        });
    }
};
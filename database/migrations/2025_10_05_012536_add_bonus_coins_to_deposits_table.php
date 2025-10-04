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
        Schema::table('deposits', function (Blueprint $table) {
            $table->integer('base_coins')->default(0)->after('coins');
            $table->integer('bonus_coins')->default(0)->after('base_coins');
            $table->integer('total_coins')->default(0)->after('bonus_coins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['base_coins', 'bonus_coins', 'total_coins']);
        });
    }
};
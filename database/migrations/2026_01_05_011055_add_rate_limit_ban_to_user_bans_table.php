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
        Schema::table('user_bans', function (Blueprint $table) {
            $table->boolean('rate_limit_ban')->default(false)->after('permanent_ban_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_bans', function (Blueprint $table) {
            $table->dropColumn('rate_limit_ban');
        });
    }
};

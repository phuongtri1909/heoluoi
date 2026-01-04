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
            $table->unsignedInteger('temp_ban_count')->default(0)->after('read_banned_until');
            $table->unsignedInteger('permanent_ban_count')->default(0)->after('temp_ban_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_bans', function (Blueprint $table) {
            $table->dropColumn(['temp_ban_count', 'permanent_ban_count']);
        });
    }
};

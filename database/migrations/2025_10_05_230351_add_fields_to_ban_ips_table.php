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
        Schema::table('ban_ips', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('user_id');
            $table->unsignedBigInteger('banned_by')->nullable()->after('reason');
            $table->timestamp('banned_at')->nullable()->after('banned_by');
            
            $table->foreign('banned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ban_ips', function (Blueprint $table) {
            $table->dropForeign(['banned_by']);
            $table->dropColumn(['reason', 'banned_by', 'banned_at']);
        });
    }
};

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
            // Drop existing foreign key constraint
            $table->dropForeign(['bank_id']);
            
            // Add new foreign key constraint to bank_autos table
            $table->foreign('bank_id')->references('id')->on('bank_autos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_auto_deposits', function (Blueprint $table) {
            // Drop the bank_autos foreign key
            $table->dropForeign(['bank_id']);
            
            // Restore original foreign key to banks table
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
        });
    }
};
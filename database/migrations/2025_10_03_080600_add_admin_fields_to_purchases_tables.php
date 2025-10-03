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
        Schema::table('story_purchases', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->enum('added_by', ['user', 'admin'])->default('user');
        });

        Schema::table('chapter_purchases', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->enum('added_by', ['user', 'admin'])->default('user'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('story_purchases', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['admin_id', 'reference_id', 'notes', 'added_by']);
        });

        Schema::table('chapter_purchases', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['admin_id', 'reference_id', 'notes', 'added_by']);
        });
    }
};

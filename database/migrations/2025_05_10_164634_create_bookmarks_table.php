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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->boolean('notification_enabled')->default(true);
            $table->foreignId('last_chapter_id')->nullable()->constrained('chapters')->onDelete('set null');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            
            // Ensure each user can only bookmark a story once
            $table->unique(['user_id', 'story_id']);
        }, 'ENGINE=InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
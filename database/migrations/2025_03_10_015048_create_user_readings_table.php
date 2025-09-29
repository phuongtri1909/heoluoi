<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('progress_percent')->default(0);
            $table->string('session_id')->nullable()->index();
            $table->timestamps();
            
            // Đảm bảo mỗi người dùng chỉ có một bản ghi cho mỗi truyện
            $table->unique(['user_id', 'story_id']);
            // Hoặc một session chỉ có một bản ghi cho mỗi truyện
            $table->unique(['session_id', 'story_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_readings');
    }
}

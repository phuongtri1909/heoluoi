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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('cover');
            $table->string('cover_thumbnail');
            $table->boolean('completed')->default(false);
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->boolean('is_18_plus')->default(false);
            $table->integer('combo_price')->nullable()->default(0)->comment('Giá combo của truyện');
            $table->boolean('has_combo')->default(false)->comment('Trạng thái đã có combo hay chưa');
            $table->boolean('is_featured')->default(false)->comment('Truyện đề cử');
            $table->integer('featured_order')->nullable()->comment('Thứ tự hiển thị truyện đề cử (số nhỏ hiển thị trước)');
            
            // Index cho performance
            $table->index(['is_featured', 'featured_order']);
            $table->index('featured_order');
            $table->timestamps();
        }, 'ENGINE=InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};

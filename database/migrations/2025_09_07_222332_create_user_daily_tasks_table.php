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
        Schema::create('user_daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_task_id')->constrained()->onDelete('cascade');
            $table->date('task_date');
            $table->integer('completed_count')->default(0);
            $table->integer('coin_reward')->default(0)->comment('Số cám thưởng tại thời điểm hoàn thành nhiệm vụ');
            $table->timestamp('last_completed_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'daily_task_id', 'task_date']);
            
            $table->index(['user_id', 'task_date']);
            $table->index(['daily_task_id', 'task_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_daily_tasks');
    }
};

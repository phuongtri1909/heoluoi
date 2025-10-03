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
        Schema::create('coin_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_admin_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount');
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('completed'); 
            $table->timestamps();
            $table->index(['from_admin_id', 'created_at']);
            $table->index(['to_user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transfers');
    }
};
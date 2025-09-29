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
        Schema::create('coin_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('amount')->comment('Số xu (dương = cộng, âm = trừ)');
            $table->enum('type', ['add', 'subtract'])->comment('Loại giao dịch');
            $table->string('transaction_type')->comment('Loại giao dịch chi tiết');
            $table->text('description')->nullable()->comment('Mô tả chi tiết');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID tham chiếu');
            $table->string('reference_type')->nullable()->comment('Loại tham chiếu');
            $table->integer('balance_before')->comment('Số dư trước giao dịch');
            $table->integer('balance_after')->comment('Số dư sau giao dịch');
            $table->string('ip_address')->nullable()->comment('IP thực hiện giao dịch');
            $table->text('user_agent')->nullable()->comment('User agent');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'transaction_type']);
            $table->index(['reference_id', 'reference_type']);
            $table->index('created_at');
            $table->index('transaction_type');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_histories');
    }
};

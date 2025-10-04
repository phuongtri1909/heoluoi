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
        Schema::create('bank_auto_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->constrained()->onDelete('cascade');
            $table->string('transaction_code')->unique();
            $table->decimal('amount', 10, 2);
            $table->integer('base_coins')->comment('Cám cơ bản');
            $table->integer('bonus_coins')->comment('Cám bonus');
            $table->integer('total_coins')->comment('Tổng cám');
            $table->unsignedInteger('fee_amount')->comment('Phí giao dịch');
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('casso_response')->nullable()->comment('Response từ Casso API');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_auto_deposits');
    }
};

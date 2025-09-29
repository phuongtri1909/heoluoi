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
        Schema::create('request_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->constrained();
            $table->string('transaction_code')->unique();
            $table->unsignedBigInteger('amount');
            $table->unsignedInteger('coins')->comment('số lượng xu sau khi trừ phí');
            $table->unsignedInteger('fee')->comment('phí giao dịch');
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('deposit_id')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_payments');
    }
};

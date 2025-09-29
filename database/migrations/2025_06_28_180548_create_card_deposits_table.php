<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('Loại thẻ: VIETTEL, MOBIFONE, VINAPHONE'); 
            $table->string('serial')->comment('Số serial thẻ');
            $table->string('pin')->comment('Mã pin thẻ');
            $table->integer('amount')->comment('Mệnh giá thẻ');
            $table->integer('coins')->comment('Số xu nhận được');
            $table->decimal('fee_percent', 5, 2)->comment('Phí giao dịch (%)');
            $table->integer('fee_amount')->comment('Số tiền phí');
            $table->decimal('penalty_amount', 15, 2)->nullable()->comment('Số tiền phạt khi sai mệnh giá');
            $table->decimal('penalty_percent', 5, 2)->nullable()->comment('Phần trăm phạt khi sai mệnh giá');
            $table->string('request_id')->unique()->comment('Mã giao dịch gửi lên TSR');
            $table->string('transaction_id')->nullable()->comment('Mã giao dịch từ TSR');
            $table->enum('status', ['pending', 'processing', 'success', 'failed'])->default('pending');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('response_data')->nullable()->comment('Dữ liệu phản hồi từ API');
            $table->text('note')->nullable()->comment('Ghi chú lỗi');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('request_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_deposits');
    }
};
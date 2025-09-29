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
        Schema::create('request_payment_paypals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('payment_type', ['paypal', 'bank'])->default('paypal');
            $table->decimal('usd_amount', 10, 2)->nullable(); // Cho PayPal
            $table->decimal('vnd_amount', 15, 0); // Số tiền VNĐ
            $table->integer('coins'); // Số xu sẽ nhận được
            $table->decimal('exchange_rate', 10, 2)->nullable(); // Tỷ giá USD -> VNĐ
            $table->decimal('fee_percent', 5, 2)->default(0); // Phí %
            $table->decimal('fee_amount', 15, 0)->default(0); // Phí VNĐ
            $table->string('transaction_code', 20)->unique(); // Mã giao dịch
            $table->string('paypal_email')->nullable(); // Email PayPal
            $table->text('paypal_me_link')->nullable(); // Link PayPal.me
            $table->string('content')->nullable(); // Nội dung chuyển khoản
            $table->enum('status', ['pending', 'confirmed', 'expired', 'cancelled'])->default('pending');
            $table->text('note')->nullable(); // Ghi chú
            $table->timestamp('expired_at')->nullable(); // Thời hạn thanh toán
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('transaction_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_payment_paypals');
    }
};

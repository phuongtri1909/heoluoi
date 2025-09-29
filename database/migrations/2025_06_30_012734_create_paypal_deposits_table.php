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
        Schema::create('paypal_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('request_payment_paypal_id')->nullable()->constrained('request_payment_paypals')->onDelete('set null');
            $table->decimal('usd_amount', 10, 2); 
            $table->decimal('vnd_amount', 15, 0); 
            $table->integer('coins'); 
            $table->decimal('exchange_rate', 10, 2);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->decimal('fee_amount', 15, 0)->default(0);
            $table->string('transaction_code', 20)->unique();
            $table->string('paypal_email')->nullable(); 
            $table->text('image')->nullable(); 
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
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
        Schema::dropIfExists('paypal_deposits');
    }
};

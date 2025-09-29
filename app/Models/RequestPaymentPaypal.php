<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPaymentPaypal extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'payment_type',
        'base_usd_amount',
        'usd_amount',
        'payment_method',
        'vnd_amount',
        'coins',
        'exchange_rate',
        'fee_percent',
        'fee_amount',
        'transaction_code',
        'paypal_email',
        'paypal_me_link',
        'content',
        'status',
        'note',
        'expired_at',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'usd_amount' => 'decimal:2',
        'vnd_amount' => 'decimal:0',
        'exchange_rate' => 'decimal:2',
        'fee_percent' => 'decimal:2',
        'fee_amount' => 'decimal:0',
        'expired_at' => 'datetime',
    ];

    // Payment types
    const TYPE_PAYPAL = 'paypal';
    const TYPE_BANK = 'bank';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get user who made the request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get related PayPal deposit
     */
    public function paypalDeposit()
    {
        return $this->hasOne(PaypalDeposit::class, 'request_payment_paypal_id');
    }

    /**
     * Get formatted USD amount
     */
    public function getUsdAmountFormattedAttribute()
    {
        return '$' . number_format($this->usd_amount, 2);
    }

    /**
     * Get formatted VND amount
     */
    public function getVndAmountFormattedAttribute()
    {
        return number_format($this->vnd_amount) . ' VNĐ';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ thanh toán',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_EXPIRED => 'Đã hết hạn',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_CONFIRMED => 'bg-success',
            self::STATUS_EXPIRED => 'bg-danger',
            self::STATUS_CANCELLED => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    /**
     * Check if request is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Generate unique transaction code
     */
    public static function generateTransactionCode($type = 'PP')
    {
        do {
            $code = $type . strtoupper(substr(md5(time() . rand()), 0, 8));
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }

    /**
     * Generate payment content
     */
    public function generatePaymentContent()
    {
        return "NAP XU {$this->transaction_code} {$this->usd_amount_formatted}";
    }

    /**
     * Mark as confirmed and create PayPal deposit
     */
    public function confirmPayment($evidenceImage = null)
    {
        // Update status
        $this->update([
            'status' => self::STATUS_CONFIRMED
        ]);

        // Create PayPal deposit
        $paypalDeposit = PaypalDeposit::create([
            'user_id' => $this->user_id,
            'request_payment_paypal_id' => $this->id,
            'usd_amount' => $this->usd_amount,
            'base_usd_amount' => $this->base_usd_amount,
            'payment_method' => $this->payment_method,
            'vnd_amount' => $this->vnd_amount,
            'coins' => $this->coins,
            'exchange_rate' => $this->exchange_rate,
            'fee_percent' => $this->fee_percent,
            'fee_amount' => $this->fee_amount,
            'transaction_code' => $this->transaction_code,
            'paypal_email' => $this->paypal_email,
            'image' => $evidenceImage,
            'status' => PaypalDeposit::STATUS_PROCESSING,
            'note' => '',
            'processed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return $paypalDeposit;
    }
}

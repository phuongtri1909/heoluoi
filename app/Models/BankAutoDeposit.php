<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAutoDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'transaction_code',
        'casso_transaction_id',
        'amount',
        'base_coins',
        'bonus_coins',
        'total_coins',
        'fee_amount',
        'status',
        'note',
        'processed_at',
        'casso_response'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'casso_response' => 'array'
    ];

    /**
     * Get the user who made the deposit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bank
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Đang xử lý',
            self::STATUS_SUCCESS => 'Thành công',
            self::STATUS_FAILED => 'Thất bại',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    /**
     * Get formatted amount
     */
    public function getAmountFormattedAttribute()
    {
        return number_format($this->amount) . ' VNĐ';
    }

    /**
     * Get total coins formatted
     */
    public function getTotalCoinsFormattedAttribute()
    {
        return number_format($this->total_coins) . ' cám';
    }

    /**
     * Get bonus coins formatted
     */
    public function getBonusCoinsFormattedAttribute()
    {
        return number_format($this->bonus_coins) . ' cám';
    }

    /**
     * Get base coins formatted
     */
    public function getBaseCoinsFormattedAttribute()
    {
        return number_format($this->base_coins) . ' cám';
    }

    /**
     * Check if deposit is successful
     */
    public function isSuccessful()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if deposit is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if deposit is failed
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if deposit is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Scope for successful deposits
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope for pending deposits
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed deposits
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for cancelled deposits
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
}
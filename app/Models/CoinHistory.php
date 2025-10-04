<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'transaction_type',
        'description',
        'reference_id',
        'reference_type',
        'balance_before',
        'balance_after',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo('reference');
    }

    /**
     * Transaction types constants
     */
    const TYPE_CARD_DEPOSIT = 'card_deposit';
    const TYPE_PAYPAL_DEPOSIT = 'paypal_deposit';
    const TYPE_BANK_DEPOSIT = 'bank_deposit';
    const TYPE_BANK_AUTO_DEPOSIT = 'bank_auto_deposit';
    const TYPE_CHAPTER_PURCHASE = 'chapter_purchase';
    const TYPE_STORY_PURCHASE = 'story_purchase';
    const TYPE_CHAPTER_EARNINGS = 'chapter_earnings';
    const TYPE_STORY_EARNINGS = 'story_earnings';
    const TYPE_FEATURED_STORY = 'featured_story';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_WITHDRAWAL_REFUND = 'withdrawal_refund';
    const TYPE_DAILY_TASK = 'daily_task';
    const TYPE_REFUND = 'refund';
    const TYPE_BONUS = 'bonus';

    /**
     * Get transaction type label
     */
    public function getTransactionTypeLabelAttribute()
    {
        $labels = [
            self::TYPE_CARD_DEPOSIT => 'Nạp thẻ',
            self::TYPE_PAYPAL_DEPOSIT => 'Nạp PayPal',
            self::TYPE_BANK_DEPOSIT => 'Nạp chuyển khoản',
            self::TYPE_BANK_AUTO_DEPOSIT => 'Nạp bank auto',
            self::TYPE_CHAPTER_PURCHASE => 'Mua chương',
            self::TYPE_STORY_PURCHASE => 'Mua combo truyện',
            self::TYPE_CHAPTER_EARNINGS => 'Thu nhập từ chương',
            self::TYPE_STORY_EARNINGS => 'Thu nhập từ truyện',
            self::TYPE_FEATURED_STORY => 'Đề cử truyện',
            self::TYPE_WITHDRAWAL => 'Rút tiền',
            self::TYPE_WITHDRAWAL_REFUND => 'Hoàn tiền rút',
            self::TYPE_DAILY_TASK => 'Nhiệm vụ hàng ngày',
            self::TYPE_REFUND => 'Hoàn tiền',
            self::TYPE_BONUS => 'Thưởng',
        ];

        return $labels[$this->transaction_type] ?? $this->transaction_type;
    }

    /**
     * Get formatted amount with sign
     */
    public function getFormattedAmountAttribute()
    {
        $sign = $this->type === 'add' ? '+' : '-';
        return $sign . number_format($this->amount);
    }

    /**
     * Scope by transaction type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for income (add)
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'add');
    }

    /**
     * Scope for expense (subtract)
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'subtract');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CardDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'serial',
        'pin',
        'amount',
        'coins',
        'fee_percent',
        'fee_amount',
        'request_id',
        'transaction_id',
        'status',
        'response_data',
        'note',
        'processed_at',
        'penalty_amount',
        'penalty_percent',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'response_data' => 'array'
    ];

    // Loại thẻ được hỗ trợ (theo TSR)
    const CARD_TYPES = [
        'VIETTEL' => 'Viettel',
        // 'GARENA' => 'Garena',
        // 'ZING' => 'Zing',
        'VINAPHONE' => 'Vinaphone',
        'MOBIFONE' => 'Mobifone',
        'VNMOBI' => 'Vietnamobile',
        // 'VCOIN' => 'Vcoin',
        // 'SCOIN' => 'Scoin',
        // 'APPOTA' => 'Appota',  
    ];

    // Mệnh giá thẻ được hỗ trợ (theo TSR)
    const CARD_VALUES = [
        10000 => '10.000 VNĐ',
        20000 => '20.000 VNĐ',
        30000 => '30.000 VNĐ',
        50000 => '50.000 VNĐ',
        100000 => '100.000 VNĐ',
        200000 => '200.000 VNĐ',
        300000 => '300.000 VNĐ',
        500000 => '500.000 VNĐ',
        // 1000000 => '1.000.000 VNĐ'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Đang chờ',
            'processing' => 'Đang xử lý',
            'success' => 'Thành công',
            'failed' => 'Thất bại',
            default => 'Không xác định'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info',
            'success' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getCardTypeNameAttribute()
    {
        return self::CARD_TYPES[$this->type] ?? $this->type;
    }

    public function getAmountFormattedAttribute()
    {
        return number_format($this->amount) . ' VNĐ';
    }

    public function getCoinsFormattedAttribute()
    {
        return number_format($this->coins) . ' xu';
    }

    // Đánh dấu giao dịch thành công
    public function markAsSuccess($transactionId = null)
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'transaction_id' => $transactionId,
            'processed_at' => now()
        ]);
    }

    // Đánh dấu giao dịch thất bại
    public function markAsFailed($note = null, $responseData = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'note' => $note,
            'response_data' => $responseData,
            'processed_at' => now()
        ]);
    }

    // Đánh dấu đang xử lý
    public function markAsProcessing($transactionId = null, $responseData = null)
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'transaction_id' => $transactionId,
            'response_data' => $responseData
        ]);
    }

    public function getPenaltyAmountFormattedAttribute()
    {
        return $this->penalty_amount ? number_format($this->penalty_amount) . 'đ' : null;
    }
    public function hasPenalty()
    {
        return $this->penalty_amount > 0;
    }

    public function getTotalDeductionAttribute()
    {
        return $this->fee_amount + ($this->penalty_amount ?? 0);
    }

    public function getEffectiveAmountAttribute()
    {
        return $this->amount - $this->total_deduction;
    }
}

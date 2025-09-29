<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'transaction_code',
        'amount',
        'coins',
        'fee',
        'is_completed',
        'deposit_id',
        'expired_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    // Kiểm tra xem yêu cầu đã hết hạn chưa
     public function isExpired()
    {
        $appTimezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($appTimezone);
        
        // Convert expired_at về múi giờ của app để so sánh
        $expiredAt = $this->expired_at->setTimezone($appTimezone);
        
        return $now->greaterThan($expiredAt);
    }

    // Đánh dấu là đã hoàn thành và liên kết với deposit
    public function markAsCompleted($depositId)
    {
        $this->update([
            'is_completed' => true,
            'deposit_id' => $depositId
        ]);
    }
}

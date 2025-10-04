<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAuto extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'account_number',
        'account_name',
        'logo',
        'qr_code',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope để lấy các ngân hàng đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? \Storage::url($this->logo) : null;
    }

    /**
     * Get the QR code URL
     */
    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? \Storage::url($this->qr_code) : null;
    }
}
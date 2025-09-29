<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'logo',
        'account_number',
        'account_name',
        'status',
        'qr_code'
    ];

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
}

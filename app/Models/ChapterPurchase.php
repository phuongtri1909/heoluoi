<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'amount_paid',
        'amount_received',
        'admin_id',
        'reference_id',
        'notes',
        'added_by'
    ];

    /**
     * Get the user who made the purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchased chapter
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Get the price (alias for amount_paid)
     */
    public function getPriceAttribute()
    {
        return $this->amount_paid;
    }

    /**
     * Get the admin who added this purchase
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

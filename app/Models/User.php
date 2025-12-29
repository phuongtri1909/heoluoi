<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'active',
        'key_active',
        'key_reset_password',
        'reset_password_at',
        'google_id',
        'ip_address',
        'recently_read',
        'coins',
        'remember_token_expires_at'
    ];

    const ROLE_USER = 'user';
    const ROLE_ADMIN_MAIN = 'admin_main';
    const ROLE_ADMIN_SUB = 'admin_sub';

    /**
     * Get all chapter purchases made by this user
     */
    public function chapterPurchases()
    {
        return $this->hasMany(ChapterPurchase::class);
    }

    /**
     * Get all story purchases made by this user
     */
    public function storyPurchases()
    {
        return $this->hasMany(StoryPurchase::class);
    }

    /**
     * Get all deposits made by this user
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get all bookmarks created by this user
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function isBanned()
    {
        $ban = $this->userBan;
        return $ban && ($ban->login || $ban->comment || $ban->rate || $ban->read);
    }

    public function userBan()
    {
        return $this->hasOne(UserBan::class)->withDefault([
            'login' => false,
            'comment' => false,
            'rate' => false,
            'read' => false,
        ]);
    }

    public function rateLimitViolations()
    {
        return $this->hasMany(RateLimitViolation::class);
    }

    public function banIps()
    {
        return $this->hasMany(BanIp::class);
    }

    public function banIp()
    {
        return $this->hasOne(BanIp::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN_MAIN;
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has a pending author application
     */
    public function hasPendingAuthorApplication()
    {
        return $this->authorApplications()->where('status', 'pending')->exists();
    }

    /**
     * Check if the user has an approved author application
     */
    public function hasApprovedAuthorApplication()
    {
        return $this->authorApplications()->where('status', 'approved')->exists();
    }

    /**
     * Get the latest author application
     */
    public function latestAuthorApplication()
    {
        return $this->authorApplications()->latest()->first();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'remember_token_expires_at' => 'datetime',
        'recently_read' => 'array'
    ];
    
    /**
     * Kiểm tra xem remember token còn hạn không
     */
    public function isRememberTokenValid(): bool
    {
        if (!$this->remember_token || !$this->remember_token_expires_at) {
            return false;
        }
        
        return $this->remember_token_expires_at->isFuture();
    }

    /**
     * Get total amount spent on chapters
     */
    public function getTotalChapterSpendingAttribute()
    {
        return $this->chapterPurchases()->sum('amount_paid');
    }

    /**
     * Get total amount spent on stories
     */
    public function getTotalStorySpendingAttribute()
    {
        return $this->storyPurchases()->sum('amount_paid');
    }

    /**
     * Get total amount deposited (including bank manual and bank auto)
     */
    public function getTotalDepositsAttribute()
    {
        $bankManual = $this->deposits()->where('status', 'approved')->sum('coins');
        $bankAuto = $this->bankAutoDeposits()->where('status', 'success')->sum('total_coins');
        return $bankManual + $bankAuto;
    }

    /**
     * Get total revenue for author (from chapters and stories they've authored)
     */
    public function getAuthorRevenueAttribute()
    {
        // Get stories authored by this user
        $storyIds = Story::where('user_id', $this->id)->pluck('id');
        
        // Calculate revenue from story purchases
        $storyRevenue = StoryPurchase::whereIn('story_id', $storyIds)->sum('amount_received');
        
        // Calculate revenue from chapter purchases
        $chapterRevenue = ChapterPurchase::whereHas('chapter', function($query) {
            $query->whereHas('story', function($query) {
                $query->where('user_id', $this->id);
            });
        })->sum('amount_received');
        
        return $storyRevenue + $chapterRevenue;
    }

    /**
     * Get coin transactions for this user (admin managed)
     */
    public function coinTransactions()
    {
        return $this->hasMany(CoinTransaction::class);
    }

    /**
     * Get coin history for this user (automatic transactions)
     */
    public function coinHistories()
    {
        return $this->hasMany(CoinHistory::class);
    }

    /**
     * Get coin transactions administered by this user
     */
    public function administeredCoinTransactions()
    {
        return $this->hasMany(CoinTransaction::class, 'admin_id');
    }

    public function paypalDeposits()
    {
        return $this->hasMany(PaypalDeposit::class);
    }

    /**
     * Get card deposits made by this user
     */
    public function cardDeposits()
    {
        return $this->hasMany(CardDeposit::class);
    }

    /**
     * Get bank auto deposits made by this user
     */
    public function bankAutoDeposits()
    {
        return $this->hasMany(BankAutoDeposit::class);
    }

    /**
     * Get user daily tasks
     */
    public function userDailyTasks()
    {
        return $this->hasMany(UserDailyTask::class);
    }
}

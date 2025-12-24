<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Config;

class Story extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'status',
        'cover',
        'cover_thumbnail',
        'completed',
        'editor_id',
        'author_name',
        'is_18_plus',
        'combo_price',
        'has_combo',
        'is_featured',
        'featured_order',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';


    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }


    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePopular($query)
    {
        return $query->withCount('chapters')->orderByDesc('chapters_count');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getTotalViewsAttribute()
    {
        // Sử dụng computed field từ database nếu có, fallback về relationship
        if (isset($this->attributes['total_views'])) {
            return $this->attributes['total_views'];
        }
        if (isset($this->attributes['chapters_sum_views'])) {
            return $this->attributes['chapters_sum_views'];
        }
        // Tránh lazy loading - dùng query builder thay vì relationship
        if ($this->relationLoaded('chapters')) {
        return $this->chapters->sum('views');
        }
        return $this->chapters()->sum('views');
    }

    public function getAverageViewsAttribute()
    {
        // Sử dụng computed fields từ database nếu có
        $chaptersCount = $this->attributes['chapters_count'] ?? $this->chapters_count;
        $totalViews = $this->attributes['total_views'] ?? $this->total_views;
        
        return $chaptersCount > 0 ? $totalViews / $chaptersCount : 0;
    }

    public function latestChapter()
    {
        return $this->hasOne(Chapter::class)
            ->where('status', self::STATUS_PUBLISHED)
            ->orderByDesc('number');
    }

    /**
     * Get the latest published chapter's published_at date
     */
    public function getLatestPublishedAtAttribute()
    {
        $latestChapter = $this->chapters()
            ->where('status', 'published')
            ->orderByDesc('number')
            ->first();
            
        return $latestChapter ? $latestChapter->published_at : $this->created_at;
    }
    /**
     * Get the bookmarks for the story.
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    /**
     * Trạng thái hoàn thành để tạo combo
     */
    public function getCanCreateComboAttribute()
    {
        // Sử dụng computed field từ database nếu có, fallback về query
        if (isset($this->attributes['chapters_count'])) {
            return $this->completed && $this->attributes['chapters_count'] > 0;
        }
        return $this->completed && $this->chapters()->where('status', 'published')->count() > 0;
    }

    /**
     * Check if the story has a combo
     */
    public function hasCombo()
    {
        return $this->has_combo;
    }

    /**
     * Get the total chapter price
     */
    public function getTotalChapterPriceAttribute()
    {
        // Sử dụng computed field từ database nếu có, fallback về query
        if (isset($this->attributes['total_chapter_price'])) {
            return $this->attributes['total_chapter_price'];
        }
        // Kiểm tra vớiSum result
        if (isset($this->attributes['chapters_sum_price'])) {
            return $this->attributes['chapters_sum_price'];
        }
        return $this->chapters()->where('status', 'published')->sum('price');
    }

    /**
     * Get discount percentage for combo
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_combo || $this->combo_price <= 0 || $this->total_chapter_price <= 0) {
            return 0;
        }

        $discount = (($this->total_chapter_price - $this->combo_price) / $this->total_chapter_price) * 100;
        return round($discount);
    }

    public function purchases()
    {
        return $this->hasMany(StoryPurchase::class);
    }

    /**
     * Check if a user has purchased this story combo
     */
    public function isPurchasedBy($userId)
    {
        return $this->purchases()->where('user_id', $userId)->exists();
    }

    public function storyPurchases()
    {
        return $this->hasMany(StoryPurchase::class);
    }

    public function chapterPurchases()
    {
        return $this->hasManyThrough(
            ChapterPurchase::class,
            Chapter::class,
            'story_id',
            'chapter_id',
            'id',
            'id'
        );
    }


    public function getIsFeaturedAttribute($value)
    {
        return (bool) $value;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('featured_order');
    }

    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope to hide 18+ stories if config is enabled
     */
    public function scopeHide18Plus($query)
    {
        $hide18Plus = Config::getConfig('hide_story_18_plus', 0);
        
        if ($hide18Plus == 1) {
            return $query->where('is_18_plus', false);
        }
        return $query;
    }

    public static function getNextFeaturedOrder()
    {
        return self::where('is_featured', true)->max('featured_order') + 1;
    }

    /**
     * Check if story is featured
     */
    public function isAnyFeatured()
    {
        return $this->is_featured;
    }

    /**
     * Get featured status text
     */
    public function getFeaturedStatusTextAttribute()
    {
        return $this->is_featured ? 'Admin đề cử' : 'Thường';
    }

    /**
     * Get featured badge
     */
    public function getFeaturedBadgeAttribute()
    {
        if ($this->is_featured) {
            return '<span class="badge bg-gradient-warning">Admin đề cử #' . $this->featured_order . '</span>';
        }
        return '';
    }

    protected $with = ['categories'];
}

<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Story;
use App\Models\Banner;
use App\Models\Donate;
use App\Models\Rating;
use App\Models\Social;
use App\Models\Status;
use App\Models\Chapter;
use App\Models\Socials;
use App\Models\Category;
use App\Models\StoryPurchase;
use App\Models\ChapterPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Static variables để tránh duplicate queries trong cùng request
     */
    private static $categories = null;
    private static $banners = null;
    private static $donate = null;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Composer cho categories - chỉ load khi cần
        View::composer([
            'layouts.partials.header',
            'pages.home',
            'pages.search.results',
            'layouts.partials.footer',
            'pages.chapter',
            'pages.information.author.author_create',
            'pages.information.author.author_edit',
        ], function ($view) {
            $view->with('categories', $this->getCategories());
        });

        // Composer cho top purchased stories - load cho trang home và chapter
        View::composer(['pages.home', 'pages.chapter'], function ($view) {
            $topStories = $this->getTopStories();
            $view->with('dailyTopPurchased', $topStories['daily']);
            $view->with('weeklyTopPurchased', $topStories['weekly']);
            $view->with('monthlyTopPurchased', $topStories['monthly']);
        });

        // Composer cho banners - chỉ load khi cần
        View::composer([
            'pages.home',
            'layouts.partials.header',
        ], function ($view) {
            $view->with('banners', $this->getBanners());
        });

    }

    /**
     * Lấy categories - tránh duplicate trong cùng request
     */
    private function getCategories()
    {
        if (self::$categories === null) {
            self::$categories = Category::withCount(['stories' => function ($query) {
                $query->where('status', 'published');
            }])->orderBy('name')->get();
        }
        return self::$categories;
    }

    /**
     * Lấy top stories - tối ưu để tránh duplicate queries
     */
    private function getTopStories()
    {
        $today = Carbon::today();
        $weekAgo = $today->copy()->subDays(7);
        $monthAgo = $today->copy()->subDays(30);

        $dailyIds = $this->getTopStoryIds($today);
        $weeklyIds = $this->getTopStoryIds($weekAgo);
        $monthlyIds = $this->getTopStoryIds($monthAgo);

        $allStoryIds = collect([$dailyIds, $weeklyIds, $monthlyIds])
            ->flatten()
            ->unique()
            ->values();

        if ($allStoryIds->isEmpty()) {
            return [
                'daily' => collect(),
                'weekly' => collect(),
                'monthly' => collect(),
            ];
        }

        $stories = Story::whereIn('id', $allStoryIds)
            ->where('status', 'published')
            ->withCount(['chapters' => function ($query) {
                $query->where('status', 'published');
            }])
            ->with([
                'categories:id,name,slug',
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->get()
            ->keyBy('id');

        $allStoryIdsArray = $allStoryIds->toArray();
        $purchaseData = DB::select("
            SELECT story_id, SUM(purchase_count) as total_purchases, MAX(latest) as latest_purchase_at
            FROM (
                SELECT story_id, COUNT(*) as purchase_count, MAX(created_at) as latest
                FROM story_purchases 
                WHERE story_id IN (" . implode(',', $allStoryIdsArray) . ")
                GROUP BY story_id
                UNION ALL
                SELECT chapters.story_id, COUNT(*) as purchase_count, MAX(chapter_purchases.created_at) as latest
                FROM chapter_purchases 
                INNER JOIN chapters ON chapter_purchases.chapter_id = chapters.id
                WHERE chapters.story_id IN (" . implode(',', $allStoryIdsArray) . ")
                GROUP BY chapters.story_id
            ) as combined_purchases
            GROUP BY story_id
        ");
        
        $purchaseData = collect($purchaseData)->keyBy('story_id');

        $stories->each(function ($story) use ($purchaseData) {
            $purchase = $purchaseData->get($story->id);
            if ($purchase) {
                $story->total_purchases = $purchase->total_purchases;
                $story->latest_purchase_at = $purchase->latest_purchase_at;
                $story->latest_purchase_diff = $purchase->latest_purchase_at
                    ? \Carbon\Carbon::parse($purchase->latest_purchase_at)->diffForHumans()
                    : 'Chưa có ai mua';
            } else {
                $story->total_purchases = 0;
                $story->latest_purchase_at = null;
                $story->latest_purchase_diff = 'Chưa có ai mua';
            }
        });

        return [
            'daily' => $dailyIds->map(fn($id) => $stories->get($id))->filter(),
            'weekly' => $weeklyIds->map(fn($id) => $stories->get($id))->filter(),
            'monthly' => $monthlyIds->map(fn($id) => $stories->get($id))->filter(),
        ];
    }

    /**
     * Lấy banners - tránh duplicate trong cùng request
     */
    private function getBanners()
    {
        if (self::$banners === null) {
            self::$banners = Banner::active()
                ->with(['story' => function ($query) {
                    $query->select('id', 'slug', 'is_18_plus', 'title');
                }])
                ->select('id', 'image', 'link', 'story_id')
                ->get();
        }
        return self::$banners;
    }

    /**
     * Lấy top story IDs theo ngày (chỉ lấy IDs, không load data)
     */
    private function getTopStoryIds($fromDate)
    {
        $storyIds = DB::select("
            SELECT story_id, SUM(purchase_count) as total_purchases
            FROM (
                SELECT story_id, COUNT(*) as purchase_count
                FROM story_purchases 
                WHERE created_at >= ?
                GROUP BY story_id
                UNION ALL
                SELECT chapters.story_id, COUNT(*) as purchase_count
                FROM chapter_purchases 
                INNER JOIN chapters ON chapter_purchases.chapter_id = chapters.id
                WHERE chapter_purchases.created_at >= ?
                GROUP BY chapters.story_id
            ) as combined_purchases
            GROUP BY story_id
            ORDER BY total_purchases DESC
            LIMIT 10
        ", [$fromDate, $fromDate]);
        
        return collect($storyIds)->pluck('story_id');
    }

}

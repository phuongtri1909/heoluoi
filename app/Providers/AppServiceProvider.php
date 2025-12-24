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
use App\Models\Config;
use App\Observers\ConfigObserver;
use App\Observers\StoryObserver;
use App\Observers\ChapterObserver;
use App\Observers\LogoSiteObserver;
use App\Observers\CommentObserver;
use App\Observers\BookmarkObserver;
use App\Observers\RatingObserver;
use App\Observers\PurchaseObserver;
use App\Observers\ChapterPurchaseObserver;
use App\Services\ConfigService;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Console\Scheduling\Schedule;

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

        // Register Observers để clear cache khi data thay đổi
        Config::observe(ConfigObserver::class);
        Story::observe(StoryObserver::class);
        Chapter::observe(ChapterObserver::class);
        \App\Models\LogoSite::observe(LogoSiteObserver::class);
        \App\Models\Comment::observe(CommentObserver::class);
        \App\Models\Bookmark::observe(BookmarkObserver::class);
        \App\Models\Rating::observe(RatingObserver::class);
        \App\Models\StoryPurchase::observe(PurchaseObserver::class);
        \App\Models\ChapterPurchase::observe(ChapterPurchaseObserver::class);

        // ============================================
        // Eloquent Strict Mode - Tối ưu và phát hiện lỗi
        // ============================================
        $this->configureEloquentStrictMode();

        // ============================================
        // Database Query Monitoring - Phát hiện query chậm
        // ============================================
        $this->configureQueryMonitoring();

        // ============================================
        // Request/Command Lifecycle Monitoring
        // ============================================
        $this->configureLifecycleMonitoring();

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

        // Share logo với tất cả views - cache để giảm queries
        View::share('logoSite', $this->getLogoSite());

    }

    /**
     * Lấy logo site - cache 1 giờ để giảm queries
     */
    private function getLogoSite()
    {
        return Cache::remember('app_logo_site', 3600, function () {
            return \App\Models\LogoSite::first();
        });
    }

    /**
     * Lấy categories - cache 5 phút để giảm queries
     */
    private function getCategories()
    {
        if (self::$categories === null) {
            self::$categories = Cache::remember('app_categories_with_count', 300, function () {
                return Category::withCount(['stories' => function ($query) {
                    $query->where('status', 'published');
                }])->orderBy('name')->get();
            });
        }
        return self::$categories;
    }

    /**
     * Lấy top stories - cache 1 phút để giảm queries lớn
     */
    private function getTopStories()
    {
        // Cache key dựa trên ngày để tự động refresh mỗi ngày
        $cacheKey = 'app_top_stories_' . Carbon::today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 60, function () {
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
            ->hide18Plus()
            ->withCount(['chapters' => function ($query) {
                $query->where('status', 'published');
            }])
            ->withSum(['chapters' => function ($query) {
                $query->where('status', 'published');
            }], 'views')
            ->withAvg('ratings as average_rating', 'rating')
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
        });
    }

    /**
     * Lấy banners - cache 5 phút để giảm queries
     */
    private function getBanners()
    {
        if (self::$banners === null) {
            $configService = new ConfigService();
            $hide18Plus = $configService->shouldHide18Plus();
            $cacheKey = 'app_banners_' . ($hide18Plus ? 'hide18' : 'all');
            
            self::$banners = Cache::remember($cacheKey, 300, function () use ($hide18Plus) {
                $query = Banner::active()
                    ->with(['story' => function ($query) {
                        $query->select('id', 'slug', 'is_18_plus', 'title');
                    }])
                    ->select('id', 'image', 'link', 'story_id');
                
                if ($hide18Plus) {
                    $query->whereHas('story', function ($q) {
                        $q->where('is_18_plus', false);
                    })->orWhereNull('story_id');
                }
                
                return $query->get();
            });
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

    /**
     * Cấu hình Eloquent Strict Mode
     * - Prevent lazy loading (N+1 queries)
     * - Prevent accessing missing attributes
     * - Prevent silently discarding attributes
     */
    private function configureEloquentStrictMode(): void
    {
        // Bật strict mode cho Eloquent (3 tính năng cùng lúc)
        Model::shouldBeStrict();

        // Trong production, log lazy loading thay vì ném exception
        if ($this->app->environment('production')) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);
                Log::warning("Attempted to lazy load [{$relation}] on model [{$class}]", [
                    'model' => $class,
                    'relation' => $relation,
                    'model_id' => $model->getKey(),
                ]);
            });
        }

        // Hai tính năng này liên quan tính đúng đắn - bật mọi môi trường
        // Prevent accessing missing attributes (khi select một vài cột)
        Model::preventAccessingMissingAttributes();
        
        // Prevent silently discarding attributes (khi fill không fillable)
        Model::preventSilentlyDiscardingAttributes();

        // Lazy loading chỉ là vấn đề hiệu năng - không chặn production
        // Ở dev/test: throw exception ngay
        // Ở production: chỉ log warning
        Model::preventLazyLoading(!$this->app->environment('production'));
    }

    /**
     * Cấu hình monitoring cho database queries
     * Phát hiện và log các query chậm
     */
    private function configureQueryMonitoring(): void
    {
        // Tổng thời gian query > 2000ms trong một request/command
        DB::whenQueryingForLongerThan(2000, function (Connection $connection) {
            Log::warning("Database queries exceeded 2 seconds", [
                'connection' => $connection->getName(),
                'queries' => $connection->getQueryLog(),
            ]);
        });

        // Log tất cả queries chậm hơn 500ms (optional - có thể comment nếu quá nhiều log)
        // DB::listen(function ($query) {
        //     if ($query->time > 500) {
        //         Log::warning('Slow query detected', [
        //             'sql' => $query->sql,
        //             'bindings' => $query->bindings,
        //             'time' => $query->time . 'ms',
        //         ]);
        //     }
        // });
    }

    /**
     * Cấu hình monitoring cho request/command lifecycle
     * Phát hiện và log các request/command chạy chậm
     * 
     * Note: Có thể implement bằng middleware hoặc event listeners
     */
    private function configureLifecycleMonitoring(): void
    {
        // Log request chậm bằng event listener
        if (!$this->app->runningInConsole()) {
            $this->app['events']->listen('Illuminate\Foundation\Http\Events\RequestHandled', function ($event) {
                // Lấy thời gian từ khi request bắt đầu (nếu có LARAVEL_START constant)
                $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT', microtime(true));
                $duration = (microtime(true) - $startTime) * 1000; // milliseconds
                
                if ($duration > 5000) {
                    Log::warning('A request took longer than 5 seconds', [
                        'path' => $event->request->path(),
                        'method' => $event->request->method(),
                        'status' => $event->response->getStatusCode(),
                        'duration' => round($duration, 2) . 'ms',
                        'ip' => $event->request->ip(),
                    ]);
                }
            });
        }
    }

}

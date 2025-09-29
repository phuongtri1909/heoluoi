<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReadingHistoryService;

class ReadingHistoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ReadingHistoryService::class, function ($app) {
            return new ReadingHistoryService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('components.recent-reads', function ($view) {
            $readingService = app(ReadingHistoryService::class);
            $view->with('recentReads', $readingService->getRecentReadings(5));
        });
    }
}
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CoinController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BanIpController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\BankAutoController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DailyTaskController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CardDepositController;
use App\Http\Controllers\Admin\CoinHistoryController;
use App\Http\Controllers\Admin\CoinTransferController;
use App\Http\Controllers\Admin\ChapterReportController;
use App\Http\Controllers\Admin\PaypalDepositController;
use App\Http\Controllers\Admin\ManualPurchaseController;
use App\Http\Controllers\Admin\RequestPaymentController;
use App\Http\Controllers\Admin\BankAutoDepositController;


Route::group(['as' => 'admin.', 'middleware' => 'block.devtools.admin'], function () {
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return 'Cache cleared';
    })->name('clear.cache');


    Route::middleware(['ban:login'])->group(function () {
        Route::group(['middleware' => 'role:admin_main,admin_sub'], function () {
            Route::post('/users/{id}/banip', [UserController::class, 'banIp'])->name('users.banip');
        });


        Route::group(['middleware' => 'role:admin_main,admin_sub'], function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard/data', [DashboardController::class, 'getStatsData'])->name('dashboard.data');

            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::PATCH('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('users/{id}/unlock-rate-limit', [UserController::class, 'unlockRateLimit'])->name('users.unlock-rate-limit');
            Route::get('/users/{id}/load-more', [UserController::class, 'loadMoreData'])->name('users.load-more');


            Route::middleware(['role:admin_main'])->group(function () {
                Route::get('coins', [CoinController::class, 'index'])->name('coins.index');
                Route::get('coin-history', [CoinHistoryController::class, 'index'])->name('coin-history.index');
                Route::get('coin-history/user/{userId}', [CoinHistoryController::class, 'showUser'])->name('coin-history.user');
                Route::get('coins/{user}/create', [CoinController::class, 'create'])->name('coins.create');
                Route::post('coins/{user}', [CoinController::class, 'store'])->name('coins.store');
                Route::get('coin-transactions', [CoinController::class, 'transactions'])->name('coin.transactions');
                
                Route::get('rate-limit', [\App\Http\Controllers\Admin\RateLimitController::class, 'index'])->name('rate-limit.index');
                Route::post('rate-limit/{id}/unlock', [\App\Http\Controllers\Admin\RateLimitController::class, 'unlock'])->name('rate-limit.unlock');
            });

            Route::resource('categories', CategoryController::class);
            Route::resource('stories', StoryController::class);
            Route::patch('/stories/{story}/toggle-featured', [StoryController::class, 'toggleFeatured'])->name('stories.toggle-featured'); // NEW
            Route::post('/stories/bulk-featured', [StoryController::class, 'bulkUpdateFeatured'])->name('stories.bulk-featured');


            Route::get('stories/{story}/chapters/bulk-create', [ChapterController::class, 'bulkCreate'])->name('stories.chapters.bulk-create');
            Route::post('stories/{story}/chapters/bulk-store', [ChapterController::class, 'bulkStore'])->name('stories.chapters.bulk-store');
            Route::post('stories/{story}/chapters/check-existing', [ChapterController::class, 'checkExisting'])->name('stories.chapters.check-existing');
            Route::post('stories/{story}/chapters/bulk-destroy', [ChapterController::class, 'bulkDestroy'])->name('stories.chapters.bulk-destroy');
            Route::post('stories/{story}/chapters/check-deletable', [ChapterController::class, 'checkDeletable'])->name('stories.chapters.check-deletable');

            Route::resource('stories.chapters', ChapterController::class);
            Route::post('stories/{story}/chapters/{chapter}/update-views', [StoryController::class, 'updateChapterViews'])->name('stories.chapters.update-views');
            Route::post('stories/{story}/chapters/bulk-update-views', [StoryController::class, 'bulkUpdateChapterViews'])->name('stories.chapters.bulk-update-views');


            Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
            Route::get('comments', [CommentController::class, 'allComments'])->name('comments.all');

            Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve')->middleware('role:admin_main,mod');
            Route::post('comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject')->middleware('role:admin_main,mod');
            Route::post('comments/batch-approve', [CommentController::class, 'approveBatch'])->name('comments.batch-approve')->middleware('role:admin_main,mod');
            Route::post('comments/batch-reject', [CommentController::class, 'rejectBatch'])->name('comments.batch-reject')->middleware('role:admin_main,mod');

            Route::resource('banners', BannerController::class);

            Route::get('logo-site', [LogoSiteController::class, 'edit'])->name('logo-site.edit');
            Route::put('logo-site', [LogoSiteController::class, 'update'])->name('logo-site.update');
            Route::delete('logo-site/delete-logo', [LogoSiteController::class, 'deleteLogo'])->name('logo-site.delete-logo');
            Route::delete('logo-site/delete-favicon', [LogoSiteController::class, 'deleteFavicon'])->name('logo-site.delete-favicon');

            // Quản lý giao dịch nạp cám (chỉ admin_main)
            Route::middleware(['role:admin_main'])->group(function () {
                Route::get('/deposits', [DepositController::class, 'adminIndex'])->name('deposits.index');
                Route::post('/deposits/{deposit}/approve', [DepositController::class, 'approve'])->name('deposits.approve');
                Route::post('/deposits/{deposit}/reject', [DepositController::class, 'reject'])->name('deposits.reject');

                // Quản lý yêu cầu thanh toán
                Route::get('/request-payments', [RequestPaymentController::class, 'adminIndex'])->name('request.payments.index');
                Route::post('/request-payments/delete-expired', [RequestPaymentController::class, 'deleteExpired'])->name('request.payments.delete-expired');

                // Quản lý ngân hàng thủ công
                Route::resource('banks', BankController::class);

                // Quản lý ngân hàng tự động
                Route::resource('bank-autos', BankAutoController::class);

                // Quản lý giao dịch bank auto (chỉ xem, không xóa)
                Route::get('/bank-auto-deposits', [BankAutoDepositController::class, 'index'])->name('bank-auto-deposits.index');
                Route::get('/bank-auto-deposits/{bankAutoDeposit}', [BankAutoDepositController::class, 'show'])->name('bank-auto-deposits.show');

                // Quản lý cấu hình hệ thống
                Route::resource('configs', ConfigController::class);

                // Quản lý Card Deposit
                Route::get('/card-deposits', [CardDepositController::class, 'adminIndex'])->name('card-deposits.index');

                // Quản lý PayPal Deposit
                Route::get('/paypal-deposits', [PaypalDepositController::class, 'adminIndex'])->name('paypal-deposits.index');
                Route::post('/paypal-deposits/{deposit}/approve', [PaypalDepositController::class, 'approve'])->name('paypal-deposits.approve');
                Route::post('/paypal-deposits/{deposit}/reject', [PaypalDepositController::class, 'reject'])->name('paypal-deposits.reject');

                // Quản lý Request Payment PayPal
                Route::get('/request-payment-paypal', [PaypalDepositController::class, 'requestPaymentIndex'])->name('request-payment-paypal.index');
                Route::post('/request-payment-paypal/delete-expired', [PaypalDepositController::class, 'deleteExpiredRequests'])->name('request-payment-paypal.delete-expired');
            });

            // Guide management
            Route::get('/guide/edit', [GuideController::class, 'edit'])->name('guide.edit');
            Route::post('/guide/update', [GuideController::class, 'update'])->name('guide.update');

            // Social Media Management
            Route::get('socials', [SocialController::class, 'index'])->name('socials.index');
            Route::post('socials', [SocialController::class, 'store'])->name('socials.store');
            Route::put('socials/{social}', [SocialController::class, 'update'])->name('socials.update');
            Route::delete('socials/{social}', [SocialController::class, 'destroy'])->name('socials.destroy');

            // Daily Tasks Management
            Route::resource('daily-tasks', DailyTaskController::class)->except('create', 'store', 'destroy');
            Route::post('daily-tasks/{dailyTask}/toggle-active', [DailyTaskController::class, 'toggleActive'])->name('daily-tasks.toggle-active');
            Route::get('daily-tasks/dt/user-progress', [DailyTaskController::class, 'userProgress'])->name('daily-tasks.user-progress');
            Route::get('daily-tasks/dt/statistics', [DailyTaskController::class, 'statistics'])->name('daily-tasks.statistics');

            // Chapter Reports Management
            Route::get('chapter-reports', [ChapterReportController::class, 'index'])->name('chapter-reports.index');
            Route::get('chapter-reports/{report}', [ChapterReportController::class, 'show'])->name('chapter-reports.show');
            Route::put('chapter-reports/{report}/status', [ChapterReportController::class, 'updateStatus'])->name('chapter-reports.update.status');
            Route::post('chapter-reports/bulk-update', [ChapterReportController::class, 'bulkUpdate'])->name('chapter-reports.bulk-update');
            Route::get('chapter-reports/stats/api', [ChapterReportController::class, 'statsApi'])->name('chapter-reports.stats.api');

            // Manual Purchases Management (chỉ admin_main)
            Route::middleware(['role:admin_main'])->group(function () {
                Route::get('manual-purchases', [ManualPurchaseController::class, 'index'])->name('manual-purchases.index');
                Route::get('manual-purchases/create', [ManualPurchaseController::class, 'create'])->name('manual-purchases.create');
                Route::post('manual-purchases', [ManualPurchaseController::class, 'store'])->name('manual-purchases.store');
                Route::delete('manual-purchases/story/{storyPurchase}', [ManualPurchaseController::class, 'destroyStoryPurchase'])->name('manual-purchases.destroy.story');
                Route::delete('manual-purchases/chapter/{chapterPurchase}', [ManualPurchaseController::class, 'destroyChapterPurchase'])->name('manual-purchases.destroy.chapter');

                // AJAX endpoints
                Route::get('manual-purchases/api/stories', [ManualPurchaseController::class, 'getStories'])->name('manual-purchases.api.stories');
                Route::get('manual-purchases/api/chapters', [ManualPurchaseController::class, 'getChapters'])->name('manual-purchases.api.chapters');
                Route::get('manual-purchases/api/users', [ManualPurchaseController::class, 'getUsers'])->name('manual-purchases.api.users');
            });

            // Coin Transfer Management (admin_sub can transfer, admin_main can monitor)
            Route::get('coin-transfers', [CoinTransferController::class, 'index'])->name('coin-transfers.index');
            Route::get('coin-transfers/create', [CoinTransferController::class, 'create'])->name('coin-transfers.create');
            Route::post('coin-transfers', [CoinTransferController::class, 'store'])->name('coin-transfers.store');
            Route::get('coin-transfers/{transfer}', [CoinTransferController::class, 'show'])->name('coin-transfers.show');
            Route::get('coin-transfers/user-suggestions', [CoinTransferController::class, 'getUserSuggestions'])->name('coin-transfers.user-suggestions');
            Route::get('coin-transfers/stats/api', [CoinTransferController::class, 'statsApi'])->name('coin-transfers.stats.api');

            // Get server time for bulk create chapters
            Route::get('get-server-time', function () {
                $vietnamTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                return response()->json([
                    'time' => $vietnamTime->format('Y-m-d\TH:i:s'),
                    'timezone' => 'Asia/Ho_Chi_Minh'
                ]);
            })->name('get-server-time');
        });
        
        Route::resource('ban-ips', BanIpController::class);
        Route::post('users/{user}/ban-ip', [BanIpController::class, 'banUserIp'])->name('users.ban-ip');
    });
});

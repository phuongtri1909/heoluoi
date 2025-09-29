<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CoinController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DailyTaskController;
use App\Http\Controllers\Admin\CardDepositController;
use App\Http\Controllers\Admin\CoinHistoryController;
use App\Http\Controllers\Admin\RequestPaymentController;
use App\Http\Controllers\Admin\PaypalDepositController;


Route::group(['as' => 'admin.'], function () {
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return 'Cache cleared';
    })->name('clear.cache');


    // Sử dụng middleware 'role' thay vì 'role.admin'
    Route::group(['middleware' => 'role:admin_main'], function () {
        Route::post('/users/{id}/banip', [UserController::class, 'banIp'])->name('users.banip');
    });

    // Sử dụng middleware 'role' thay vì 'role.admin.mod'
    Route::group(['middleware' => 'role:admin_main,mod'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'getStatsData'])->name('dashboard.data');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::PATCH('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/{id}/load-more', [UserController::class, 'loadMoreData'])->name('users.load-more');

        // Coin management routes
        Route::get('coins', [CoinController::class, 'index'])->name('coins.index');
        Route::get('coin-history', [CoinHistoryController::class, 'index'])->name('coin-history.index');
        Route::get('coin-history/user/{userId}', [CoinHistoryController::class, 'showUser'])->name('coin-history.user');
        Route::get('coins/{user}/create', [CoinController::class, 'create'])->name('coins.create');
        Route::post('coins/{user}', [CoinController::class, 'store'])->name('coins.store');

        Route::get('coin-transactions', [CoinController::class, 'transactions'])->name('coin.transactions');

        Route::resource('categories', CategoryController::class);
        Route::resource('stories', StoryController::class);
        Route::patch('/stories/{story}/toggle-featured', [StoryController::class, 'toggleFeatured'])->name('stories.toggle-featured'); // NEW
        Route::post('/stories/bulk-featured', [StoryController::class, 'bulkUpdateFeatured'])->name('stories.bulk-featured');


        Route::resource('stories.chapters', ChapterController::class);


        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::get('comments', [CommentController::class, 'allComments'])->name('comments.all');
        
        Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve')->middleware('role:admin_main,mod');
        Route::post('comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject')->middleware('role:admin_main,mod');

        Route::resource('banners', BannerController::class);

        Route::get('logo-site', [LogoSiteController::class, 'edit'])->name('logo-site.edit');
        Route::put('logo-site', [LogoSiteController::class, 'update'])->name('logo-site.update');
        Route::delete('logo-site/delete-logo', [LogoSiteController::class, 'deleteLogo'])->name('logo-site.delete-logo');
        Route::delete('logo-site/delete-favicon', [LogoSiteController::class, 'deleteFavicon'])->name('logo-site.delete-favicon');

        // Quản lý giao dịch nạp xu
        Route::get('/deposits', [DepositController::class, 'adminIndex'])->name('deposits.index');
        Route::post('/deposits/{deposit}/approve', [DepositController::class, 'approve'])->name('deposits.approve');
        Route::post('/deposits/{deposit}/reject', [DepositController::class, 'reject'])->name('deposits.reject');

        // Quản lý yêu cầu thanh toán
        Route::get('/request-payments', [RequestPaymentController::class, 'adminIndex'])->name('request.payments.index');
        Route::post('/request-payments/delete-expired', [RequestPaymentController::class, 'deleteExpired'])->name('request.payments.delete-expired');

        // Quản lý ngân hàng
        Route::resource('banks', BankController::class);

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
    });
});

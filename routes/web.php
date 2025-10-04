<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\GuideController;
use App\Http\Controllers\Client\AuthorController;
use App\Http\Controllers\Client\BannerController;
use App\Http\Controllers\Client\RatingController;
use App\Http\Controllers\Client\CommentController;
use App\Http\Controllers\Client\DepositController;
use App\Http\Controllers\Client\ReadingController;
use App\Http\Controllers\Client\SitemapController;
use App\Http\Controllers\Client\BookmarkController;
use App\Http\Controllers\Client\PurchaseController;
use App\Http\Controllers\Client\DailyTaskController;
use App\Http\Controllers\Client\StoryComboController;
use App\Http\Controllers\Client\CardDepositController;
use App\Http\Controllers\Client\CoinHistoryController;
use App\Http\Controllers\Client\PaypalDepositController;
use App\Http\Controllers\Client\RequestPaymentController;
use App\Http\Controllers\Client\BankAutoController;


Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-main.xml', [SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-stories.xml', [SitemapController::class, 'stories'])->name('sitemap.stories');
Route::get('/sitemap-chapters.xml', [SitemapController::class, 'chapters'])->name('sitemap.chapters');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');

Route::post('/card-deposit/callback', [CardDepositController::class, 'callback'])->name('card.deposit.callback');
Route::post('/bank-auto-deposit/callback', [BankAutoController::class, 'callback'])->name('user.bank.auto.deposit.callback');


// Route::get('/check-card', [CardDepositController::class, 'checkCardForm'])->name('check.card.form');
// Route::post('/check-card', [CardDepositController::class, 'checkCard'])->name('check.card');

Route::middleware(['ban:login'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/search', [HomeController::class, 'searchHeader'])->name('searchHeader');
    Route::get('/tac-gia', [HomeController::class, 'searchAuthor'])->name('search.author');
    Route::get('/chuyen-ngu', [HomeController::class, 'searchTranslator'])->name('search.translator');
    Route::get('story-new-chapter', [HomeController::class, 'showStoryNewChapter'])->name('story.new.chapter');
    Route::get('story-hot', [HomeController::class, 'showStoryHot'])->name('story.hot');
    Route::get('story-rating', [HomeController::class, 'showRatingStories'])->name('story.rating');
    Route::get('story-new', [HomeController::class, 'showStoryNew'])->name('story.new');
    Route::get('story-view', [HomeController::class, 'showStoryView'])->name('story.view');
    Route::get('story-follow', [HomeController::class, 'showStoryFollow'])->name('story.follow');
    Route::get('story-completed', [HomeController::class, 'showCompletedStories'])->name('story.completed');

    Route::get('/categories-story/{slug}', [HomeController::class, 'showStoryCategories'])->name('categories.story.show');

    Route::get('story/{slug}', [HomeController::class, 'showStory'])->name('show.page.story');
    Route::get('/story/{storyId}/chapters', [HomeController::class, 'getStoryChapters'])->name('chapters.list');

    Route::get('/banner/{banner}', [BannerController::class, 'click'])->name('banner.click');

    Route::middleware(['ban:read'])->group(function () {
        Route::get('/story/{storySlug}/chapter/{chapterSlug}', [HomeController::class, 'chapterByStory'])->name('chapter');
        Route::post('/story/{storySlug}/{chapterSlug}/check-password', [HomeController::class, 'checkChapterPassword'])->name('chapter.check-password');
        Route::get('/search-chapters', [HomeController::class, 'searchChapters'])->name('chapters.search');
        Route::post('/reading/save-progress', [ReadingController::class, 'saveProgress'])
            ->name('reading.save-progress');
    });

    // Chapter Report Routes
    Route::middleware(['auth'])->group(function () {
        Route::post('/chapter-report', [App\Http\Controllers\Client\ChapterReportController::class, 'store'])->name('chapter.report.store');
    });

    Route::post('/comments/{comment}/react', [CommentController::class, 'react'])->name('comments.react');
    Route::get('/stories/{storyId}/comments', [CommentController::class, 'loadComments'])->name('comments.load');

    // Guide routes
    Route::get('/huong-dan', [GuideController::class, 'show'])->name('guide.show');

    Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => 'auth'], function () {
        Route::get('profile', [UserController::class, 'userProfile'])->name('profile');
        Route::post('update-profile/update-name-or-phone', [UserController::class, 'updateNameOrPhone'])->name('update.name.or.phone');
        Route::post('update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
        Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');
        Route::get('/reading-history', [UserController::class, 'readingHistory'])->name('reading.history');
        Route::post('/reading-history/clear', [UserController::class, 'clearReadingHistory'])->name('reading.history.clear');
        Route::get('purchases', [UserController::class, 'userPurchases'])->name('purchases');
        Route::get('/coin-history', [CoinHistoryController::class, 'index'])->name('coin-history');
        Route::get('/chapter-reports', [App\Http\Controllers\Client\ChapterReportController::class, 'userReports'])->name('chapter.reports');

        Route::get('/bookmarks', [BookmarkController::class, 'getUserBookmarks'])->name('bookmarks');
        Route::post('/bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
        Route::get('/bookmark/status', [BookmarkController::class, 'checkStatus'])->name('bookmark.status');
        Route::post('/bookmark/update-chapter', [BookmarkController::class, 'updateCurrentChapter'])->name('bookmark.update.chapter');
        Route::post('/bookmark/remove', [BookmarkController::class, 'remove'])->name('bookmark.remove');

        // Daily Tasks Routes
        Route::get('/daily-tasks', [DailyTaskController::class, 'index'])->name('daily-tasks');
        Route::post('/daily-tasks/complete/login', [DailyTaskController::class, 'completeLogin'])->name('daily-tasks.complete.login');
        Route::post('/daily-tasks/complete/comment', [DailyTaskController::class, 'completeComment'])->name('daily-tasks.complete.comment');
        Route::post('/daily-tasks/complete/bookmark', [DailyTaskController::class, 'completeBookmark'])->name('daily-tasks.complete.bookmark');
        Route::post('/daily-tasks/complete/share', [DailyTaskController::class, 'completeShare'])->name('daily-tasks.complete.share');
        Route::get('/daily-tasks/status', [DailyTaskController::class, 'getTodayStatus'])->name('daily-tasks.status');
        Route::get('/daily-tasks/history', [DailyTaskController::class, 'getHistory'])->name('daily-tasks.history');

        // Deposit Routes
        Route::get('/deposit', [DepositController::class, 'index'])->name('deposit');
        // Request Payment Routes
        Route::post('/request-payment', [RequestPaymentController::class, 'store'])->name('request.payment.store');
        Route::post('/request-payment/confirm', [RequestPaymentController::class, 'confirm'])->name('request.payment.confirm');

        // Card Deposit Routes
        Route::get('/card-deposit', [CardDepositController::class, 'index'])->name('card.deposit');
        Route::post('/card-deposit', [CardDepositController::class, 'store'])->name('card.deposit.store');
        Route::get('/card-deposit/status/{id}', [CardDepositController::class, 'checkStatus'])->name('card.deposit.status');

        Route::get('/paypal-deposit', [PaypalDepositController::class, 'index'])->name('paypal.deposit');
        Route::post('/paypal-deposit', [PaypalDepositController::class, 'store'])->name('paypal.deposit.store');
        Route::post('/paypal-deposit/confirm', [PaypalDepositController::class, 'confirm'])->name('paypal.deposit.confirm');
        Route::get('/paypal-deposit/status/{transactionCode}', [PaypalDepositController::class, 'checkStatus'])->name('paypal.deposit.status');

        // Bank Auto Deposit Routes
        Route::get('/bank-auto-deposit', [BankAutoController::class, 'index'])->name('bank.auto.deposit');
        Route::post('/bank-auto-deposit', [BankAutoController::class, 'store'])->name('bank.auto.deposit.store');
        Route::post('/bank-auto-deposit/calculate', [BankAutoController::class, 'calculatePreview'])->name('bank.auto.deposit.calculate');
        Route::get('/bank-auto-deposit/sse', [BankAutoController::class, 'sseTransactionUpdates'])->name('bank.auto.sse');
    });

    Route::group(['middleware' => 'auth'], function () {

        Route::middleware(['ban:comment'])->group(function () {
            Route::post('/comment/store', [CommentController::class, 'storeClient'])->name('comment.store.client');
        });

        Route::middleware(['ban:rate'])->group(function () {
            Route::post('/ratings', [RatingController::class, 'storeClient'])->name('ratings.store');
            Route::get('/ratings', function () {
                abort(404);
            });
        });

        // Routes for purchasing
        Route::post('/purchase/chapter', [PurchaseController::class, 'purchaseChapter'])->name('purchase.chapter');
        Route::post('/purchase/story-combo', [PurchaseController::class, 'purchaseStoryCombo'])->name('purchase.story.combo');

        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::group(['middleware' => 'role:admin_main'], function () {
            Route::delete('delete-comments/{comment}', [CommentController::class, 'deleteComment'])->name('delete.comments');
            Route::post('/comments/{comment}/pin', [CommentController::class, 'togglePin'])->name('comments.pin');
        });
    });

    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', function () {
            return view('pages.auth.login');
        })->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');

        Route::get('/register', function () {
            return view('pages.auth.register');
        })->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');

        Route::get('/forgot-password', function () {
            return view('pages.auth.forgot-password');
        })->name('forgot-password');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

        Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
        Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    });
});

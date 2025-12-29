<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Story;
use App\Models\Config;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\StoryPurchase;
use App\Models\ChapterPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    /**
     * Constructor - ensure user is authenticated
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Purchase a chapter
     */
    public function purchaseChapter(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'chapter_id' => 'required|exists:chapters,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ: ' . $validator->errors()->first()
                ], 422);
            }

            $user = Auth::user();
            $chapter = Chapter::findOrFail($request->chapter_id);

            if (!$chapter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chương không tồn tại.'
                ], 404);
            }

            $story = $chapter->story;

            if ($user->role === 'admin_main') {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn có quyền quản trị, không cần mua chương này.',
                    'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                ]);
            }

            if ($user->role === 'admin_sub' && $story->user_id == $user->id) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn là tác giả của truyện này, không cần mua chương.',
                    'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                ]);
            }

            if ($story->user_id == $user->id) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đây là truyện của bạn, không cần mua chương này.',
                    'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                ]);
            }

            if (!$chapter->price || $chapter->price == 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chương này đã miễn phí, không thể mua.',
                    'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                ]);
            }

            $storyPurchase = StoryPurchase::where('user_id', $user->id)
                ->where('story_id', $chapter->story_id)
                ->exists();

            if ($storyPurchase) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn đã mua trọn bộ truyện này, có thể đọc tất cả các chương.',
                    'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                ]);
            }

            $existingPurchase = ChapterPurchase::where('user_id', $user->id)
                ->where('chapter_id', $chapter->id)
                ->first();

            if ($existingPurchase) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn đã mua chương này trước đó.',
                    'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                ]);
            }

            if ($user->coins < $chapter->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không đủ cám để mua chương này. Vui lòng nạp thêm.',
                    'redirect' => route('user.bank.auto.deposit')
                ], 400);
            }

            $success = false;

            try {
                DB::beginTransaction();

                if ($user->coins < $chapter->price) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Số dư không đủ để mua chương này.',
                        'redirect' => route('user.bank.auto.deposit')
                    ], 400);
                }

                $isMonopoly = array_key_exists('is_monopoly', $story->getAttributes()) ? ($story->is_monopoly ?? false) : false;
                $authorPercentage = $isMonopoly ? Config::getConfig('monopoly_author_percentage', 90) : Config::getConfig('non_monopoly_author_percentage', 70);
                $rawEarnings = ($chapter->price * $authorPercentage) / 100;
                $authorEarnings = round($rawEarnings);

                $purchase = ChapterPurchase::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'chapter_id' => $chapter->id
                    ],
                    [
                        'amount_paid' => $chapter->price,
                        'amount_received' => $authorEarnings,
                    ]
                );

                $coinService = new \App\Services\CoinService();
                $coinService->transferCoins(
                    $user,
                    $story->user,
                    $chapter->price,
                    \App\Models\CoinHistory::TYPE_CHAPTER_PURCHASE,
                    "Mua chương: {$chapter->title}",
                    $purchase
                );
                DB::commit();
                $success = true;
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();

                Log::error('Lỗi khi mua chương 1: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xử lý giao dịch. Vui lòng thử lại.'
                ], 500);
            }

            if ($success) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Mua chương thành công! Đang tải nội dung...',
                        'newBalance' => $user->coins,
                        'redirect' => route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                    ]);
                }

                return redirect()->route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug])
                    ->with('success', 'Mua chương thành công!');
            }

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý giao dịch. Vui lòng thử lại.'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi mua chương 2: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý giao dịch. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Purchase a story combo (all chapters)
     */
    public function purchaseStoryCombo(Request $request)
    {
        try {
            // Validate request data
            $validator = validator($request->all(), [
                'story_id' => 'required|exists:stories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ: ' . $validator->errors()->first()
                ], 422);
            }

            $user = Auth::user();
            $story = Story::findOrFail($request->story_id);

            if ($user->role === 'admin_main') {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn có quyền quản trị, không cần mua truyện này.',
                    'redirect' => route('show.page.story', $story->slug)
                ]);
            }

            if ($user->role === 'admin_sub' && $story->user_id == $user->id) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn là tác giả của truyện này, không cần mua.',
                    'redirect' => route('show.page.story', $story->slug)
                ]);
            }

            if ($story->user_id == $user->id) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đây là truyện của bạn, không cần mua combo này.',
                    'redirect' => route('show.page.story', $story->slug)
                ]);
            }

            if (!$story->has_combo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truyện này không có gói combo.'
                ], 400);
            }

            $existingPurchase = StoryPurchase::where('user_id', $user->id)
                ->where('story_id', $story->id)
                ->first();

            if ($existingPurchase) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn đã mua combo truyện này trước đó.',
                    'redirect' => route('show.page.story', $story->slug)
                ]);
            }

            if ($user->coins < $story->combo_price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không đủ cám để mua combo này. Vui lòng nạp thêm.',
                    'redirect' => route('user.bank.auto.deposit')
                ], 400);
            }

            $success = false;


            try {
                DB::beginTransaction();

                $freshUser = User::lockForUpdate()->find($user->id);

                $purchaseExists = StoryPurchase::where('user_id', $user->id)
                    ->where('story_id', $story->id)
                    ->exists();

                if ($purchaseExists) {
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Bạn đã mua combo truyện này.',
                        'redirect' => route('show.page.story', $story->slug)
                    ]);
                }

                if ($freshUser->coins < $story->combo_price) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Số dư không đủ để mua combo này.',
                        'redirect' => route('user.bank.auto.deposit')
                    ], 400);
                }

                $isMonopoly = array_key_exists('is_monopoly', $story->getAttributes()) ? ($story->is_monopoly ?? false) : false;
                $authorPercentage = $isMonopoly ? Config::getConfig('monopoly_author_percentage', 90) : Config::getConfig('non_monopoly_author_percentage', 70);
                $rawEarnings = ($story->combo_price * $authorPercentage) / 100;
                $authorEarnings = round($rawEarnings);

                $purchase = StoryPurchase::create([
                    'user_id' => $user->id,
                    'story_id' => $story->id,
                    'amount_paid' => $story->combo_price,
                    'amount_received' => $authorEarnings,
                ]);

                $coinService = new \App\Services\CoinService();
                $coinService->transferCoins(
                    $freshUser,
                    $story->user,
                    $story->combo_price,
                    \App\Models\CoinHistory::TYPE_STORY_PURCHASE,
                    "Mua combo truyện: {$story->title}",
                    $purchase
                );

                DB::commit();
                $success = true;

                $user->coins = $freshUser->coins;
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();

                return response()->json([
                    'success' => true,
                    'message' => 'Bạn đã mua combo truyện này.',
                    'newBalance' => $freshUser->coins,
                    'redirect' => route('show.page.story', $story->slug)
                ]);
            }


            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mua combo truyện thành công! Đang tải nội dung...',
                    'newBalance' => $user->coins,
                    'redirect' => route('show.page.story', $story->slug)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý giao dịch. Vui lòng thử lại.'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi mua combo truyện: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý giao dịch. Vui lòng thử lại.'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use App\Models\User;
use App\Models\BanIp;
use App\Models\Bookmark;
use App\Models\UserReading;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StoryPurchase;
use Illuminate\Support\Carbon;
use App\Mail\OTPUpdateUserMail;
use App\Models\ChapterPurchase;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function show($id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        if ($authUser->role === 'admin_sub') {
            if ($user->role === 'admin_main' || $user->role === 'admin_sub') {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($user->active !== 'active') {
            abort(404);
        }

        $stats = [
            'total_deposits' => $user->total_deposits,
            'total_spent' => $user->total_chapter_spending + $user->total_story_spending,
            'balance' => $user->coins
        ];

        // Chỉ load dữ liệu liên quan đến doanh thu cho admin_main
        if ($authUser->role === 'admin_main') {
            $deposits = $user->deposits()
                ->with('bank')
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'deposits_page');

            $paypalDeposits = $user->paypalDeposits()
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'paypal_deposits_page');

            $cardDeposits = $user->cardDeposits()
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'card_deposits_page');

            $chapterPurchases = $user->chapterPurchases()
                ->with(['chapter.story'])
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'chapter_page');

            $storyPurchases = $user->storyPurchases()
                ->with(['story'])
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'story_page');
        } else {
            // admin_sub không cần dữ liệu này
            $deposits = collect();
            $paypalDeposits = collect();
            $cardDeposits = collect();
            $chapterPurchases = collect();
            $storyPurchases = collect();
        }

        $bookmarks = $user->bookmarks()
            ->with(['story', 'lastChapter'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'bookmarks_page');

        $coinTransactions = $user->coinTransactions()
            ->with('admin')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'coin_page');

        $userDailyTasks = $user->userDailyTasks()
            ->with('dailyTask')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'daily_tasks_page');

        $coinHistories = $user->coinHistories()
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'coin_histories_page');

        if ($authUser->role === 'admin_main') {
            $counts = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM deposits WHERE user_id = ?) as deposits,
                    (SELECT COUNT(*) FROM paypal_deposits WHERE user_id = ?) as paypal_deposits,
                    (SELECT COUNT(*) FROM card_deposits WHERE user_id = ?) as card_deposits,
                    (SELECT COUNT(*) FROM chapter_purchases WHERE user_id = ?) as chapter_purchases,
                    (SELECT COUNT(*) FROM story_purchases WHERE user_id = ?) as story_purchases,
                    (SELECT COUNT(*) FROM bookmarks WHERE user_id = ?) as bookmarks,
                    (SELECT COUNT(*) FROM coin_transactions WHERE user_id = ?) as coin_transactions,
                    (SELECT COUNT(*) FROM user_daily_tasks WHERE user_id = ?) as user_daily_tasks,
                    (SELECT COUNT(*) FROM coin_histories WHERE user_id = ?) as coin_histories
            ", [
                $user->id, $user->id, $user->id, $user->id, $user->id, 
                $user->id, $user->id, $user->id, $user->id
            ])[0];
        } else {
            // admin_sub chỉ cần counts cơ bản
            $counts = DB::select("
                SELECT 
                    0 as deposits,
                    0 as paypal_deposits,
                    0 as card_deposits,
                    0 as chapter_purchases,
                    0 as story_purchases,
                    (SELECT COUNT(*) FROM bookmarks WHERE user_id = ?) as bookmarks,
                    (SELECT COUNT(*) FROM coin_transactions WHERE user_id = ?) as coin_transactions,
                    (SELECT COUNT(*) FROM user_daily_tasks WHERE user_id = ?) as user_daily_tasks,
                    (SELECT COUNT(*) FROM coin_histories WHERE user_id = ?) as coin_histories
            ", [
                $user->id, $user->id, $user->id, $user->id
            ])[0];
        }

        $counts = (array) $counts;

        $user->load('userBan');

        return view('admin.pages.users.show', compact(
            'user',
            'stats',
            'deposits',
            'paypalDeposits',
            'cardDeposits',
            'chapterPurchases',
            'storyPurchases',
            'bookmarks',
            'coinTransactions',
            'userDailyTasks',
            'coinHistories',
            'counts'
        ));
    }

    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);


        if ($request->has('delete_avatar') && $authUser->role === 'admin_main') {
            if (in_array($user->role, ['admin_main', 'admin_sub'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa ảnh đại diện của Admin/Mod'
                ], 403);
            }

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã xóa ảnh đại diện'
            ]);
        }

        $superAdminEmails = explode(',', env('SUPER_ADMIN_EMAILS', 'admin@gmail.com'));
        $isSuperAdmin = in_array(strtolower($authUser->email), array_map('strtolower', $superAdminEmails));

        if ($request->has('role')) {
            if (in_array(strtolower($user->email), array_map('strtolower', $superAdminEmails))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể thay đổi quyền của Super Admin'
                ], 403);
            }

            // Không được đổi chính mình
            if ($user->id === $authUser->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể thay đổi quyền của chính mình'
                ], 403);
            }

            // Phân quyền theo role
            if ($authUser->role === 'admin_main') {
                if ($isSuperAdmin) {
                    // Super Admin có thể đổi tất cả role (trừ Super Admin khác)
                    // Super Admin có thể đổi admin_main xuống admin_sub hoặc user
                } else {
                    // admin_main thường chỉ có thể đổi user thành admin_sub hoặc admin_main
                    // admin_main thường không thể đổi admin_main khác
                    if ($user->role === 'admin_main') {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Không có quyền thay đổi quyền của Admin chính'
                        ], 403);
                    }
                }
            } elseif ($authUser->role === 'admin_sub') {
                // admin_sub chỉ có thể đổi user thành admin_sub hoặc user
                // admin_sub không thể đổi admin_main
                if ($user->role === 'admin_main') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không có quyền thay đổi quyền của Admin chính'
                    ], 403);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền thực hiện'
                ], 403);
            }

            $request->validate([
                'role' => 'required|in:user,admin_main,admin_sub'
            ], [
                'role.required' => 'Trường role không được để trống',
                'role.in' => 'Giá trị không hợp lệ'
            ]);

            $user->role = $request->role;
        }

        $banTypes = ['login', 'comment', 'rate', 'read'];
        $hasBanField = false;
        foreach ($banTypes as $type) {
            $field = "ban_$type";
            if ($request->has($field)) {
                $hasBanField = true;
            }
        }

        if ($hasBanField) {
            $userBan = $user->userBan()->firstOrCreate([
                'user_id' => $user->id,
            ]);

            foreach ($banTypes as $type) {
                $field = "ban_$type";
                if ($request->has($field)) {
                    $userBan->$type = $request->boolean($field);
                }
            }

            try {
                $userBan->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật thành công'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra'
                ], 500);
            }
        }

        try {
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    public function banIp(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'ban' => 'required|in:true,false,0,1'
        ], [
            'ban.required' => 'Trường ban không được để trống',
            'ban.in' => 'Giá trị không hợp lệ'
        ]);

        if ($request->boolean('ban')) {
            if (!$user->ip_address) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy IP của người dùng'
                ], 400);
            }

            if (!BanIp::where('ip_address', $user->ip_address)->exists()) {
                BanIp::create([
                    'ip_address' => $user->ip_address,
                    'user_id' => $user->id
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Đã thêm IP vào danh sách cấm'
            ]);
        } else {
            BanIp::where('user_id', $user->id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã xóa IP khỏi danh sách cấm'
            ]);
        }
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        $query = User::query();

        $stats = [
            'total' => User::where('active', 'active')->count(),
            'admin_main' => User::where('active', 'active')->where('role', 'admin_main')->count(),
            'admin_sub' => User::where('active', 'active')->where('role', 'admin_sub')->count(),
            'user' => User::where('active', 'active')->where('role', 'user')->count(),
        ];

        // Hiển thị tất cả users cho tất cả admin
        // Không cần filter theo role nữa


        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->where('active', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pages.users.index', compact('users', 'stats'));
    }

    public function loadMoreData(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $type = $request->type;
        $page = $request->page;

        switch ($type) {
            case 'deposits':
                $data = $user->deposits()
                    ->with('bank')
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'deposits_page', $page);
                break;
            case 'paypal-deposits':
                $data = $user->paypalDeposits()
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'paypal_deposits_page', $page);
                break;
            case 'card-deposits':
                $data = $user->cardDeposits()
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'card_deposits_page', $page);
                break;
            case 'story-purchases':
                $data = $user->storyPurchases()
                    ->with(['story'])
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'story_page', $page);
                break;
            case 'chapter-purchases':
                $data = $user->chapterPurchases()
                    ->with(['chapter.story'])
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'chapter_page', $page);
                break;
            case 'bookmarks':
                $data = $user->bookmarks()
                    ->with(['story', 'lastChapter'])
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'bookmarks_page', $page);
                break;
            case 'coin-transactions':
                $data = $user->coinTransactions()
                    ->with('admin')
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'coin_page', $page);
                break;
            case 'user-daily-tasks':
                $data = $user->userDailyTasks()
                    ->with('dailyTask')
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'daily_tasks_page', $page);
                break;
            case 'author-chapter-earnings':
                $data = \App\Models\ChapterPurchase::whereHas('chapter.story', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['chapter.story', 'user'])
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'author_chapter_earnings_page', $page);
                break;
            case 'author-story-earnings':
                $data = \App\Models\StoryPurchase::whereHas('story', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['story', 'user'])
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'author_story_earnings_page', $page);
                break;
            case 'coin-histories':
                $data = $user->coinHistories()
                    ->orderByDesc('created_at')
                    ->paginate(10, ['*'], 'coin_histories_page', $page);
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        return response()->json([
            'html' => view("admin.pages.users.partials.{$type}-table", [
                'data' => $data,
                'user' => $user
            ])->render(),
            'pagination' => $data->links('components.pagination')->toHtml(),
            'has_more' => $data->hasMorePages()
        ]);
    }
}

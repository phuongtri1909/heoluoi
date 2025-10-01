<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use App\Models\User;
use App\Models\Bookmark;
use App\Models\BanIp;
use App\Models\UserReading;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OTPUpdateUserMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function show($id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        // Check permissions
        if ($authUser->role === 'admin_sub') {
            if ($user->role === 'admin_main' || $user->role === 'admin_sub') {
                abort(403, 'Unauthorized action.');
            }
        }

        // Only show active users
        if ($user->active !== 'active') {
            abort(404);
        }

        // Get financial statistics - optimized to avoid N+1 queries
        $stats = [
            'total_deposits' => $user->total_deposits,
            'total_spent' => $user->total_chapter_spending + $user->total_story_spending,
            'balance' => $user->coins,
            'author_revenue' => $user->role === 'author' ? $user->author_revenue : 0,
            'author_story_revenue' => $user->role === 'author' ? \App\Models\StoryPurchase::whereHas('story', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->sum('amount_received') : 0,
            'author_chapter_revenue' => $user->role === 'author' ? \App\Models\ChapterPurchase::whereHas('chapter.story', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->sum('amount_received') : 0,
        ];

        // Get deposits with pagination
        $deposits = $user->deposits()
            ->with('bank')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'deposits_page');

        // Get PayPal deposits with pagination
        $paypalDeposits = $user->paypalDeposits()
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'paypal_deposits_page');

        // Get card deposits with pagination
        $cardDeposits = $user->cardDeposits()
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'card_deposits_page');

        // Get chapter purchases with pagination
        $chapterPurchases = $user->chapterPurchases()
            ->with(['chapter.story'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'chapter_page');

        // Get story purchases with pagination
        $storyPurchases = $user->storyPurchases()
            ->with(['story'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'story_page');

        // Get bookmarks with pagination
        $bookmarks = $user->bookmarks()
            ->with(['story', 'lastChapter'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'bookmarks_page');

        // Get coin transactions with pagination
        $coinTransactions = $user->coinTransactions()
            ->with('admin')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'coin_page');

        // Get user daily tasks with pagination
        $userDailyTasks = $user->userDailyTasks()
            ->with('dailyTask')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'daily_tasks_page');

        // Get author earnings (if user is author)
        $authorChapterEarnings = collect();
        $authorStoryEarnings = collect();
       
        
        if ($user->role === 'author') {
            // Get chapter earnings
            $authorChapterEarnings = \App\Models\ChapterPurchase::whereHas('chapter.story', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['chapter.story', 'user'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'author_chapter_earnings_page');

            // Get story earnings
            $authorStoryEarnings = \App\Models\StoryPurchase::whereHas('story', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['story', 'user'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'author_story_earnings_page');
        }

        // Get coin history with pagination
        $coinHistories = $user->coinHistories()
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'coin_histories_page');

        // Count totals for tabs - optimized to avoid N+1 queries
        // Use single query with selectRaw to get all counts at once
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
                (SELECT COUNT(*) FROM coin_histories WHERE user_id = ?) as coin_histories,
                (SELECT COUNT(*) FROM chapter_purchases cp 
                 JOIN chapters c ON cp.chapter_id = c.id 
                 JOIN stories s ON c.story_id = s.id 
                 WHERE s.user_id = ?) as author_chapter_earnings,
                (SELECT COUNT(*) FROM story_purchases sp 
                 JOIN stories s ON sp.story_id = s.id 
                 WHERE s.user_id = ?) as author_story_earnings
        ", [
            $user->id, $user->id, $user->id, $user->id, $user->id, 
            $user->id, $user->id, $user->id, $user->id, $user->id,
            $user->id
        ])[0];

        $counts = (array) $counts;

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
            'authorChapterEarnings',
            'authorStoryEarnings',
            'coinHistories',
            'counts'
        ));
    }

    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);


        if ($request->has('delete_avatar') && $authUser->role === 'admin') {
            // Check if target user is admin or mod
            if (in_array($user->role, ['admin', 'mod'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa ảnh đại diện của Admin/Mod'
                ], 403);
            }

            // Delete avatar using Storage facade instead of File facade
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

        // Special case for admin@gmail.com (super admin)
        // Get super admin emails from env
        $superAdminEmails = explode(',', env('SUPER_ADMIN_EMAILS', 'admin@gmail.com'));
        $isSuperAdmin = in_array($authUser->email, $superAdminEmails);

        if ($request->has('role')) {
            // Prevent changing super admin's role
            if (in_array($user->email, $superAdminEmails)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể thay đổi quyền của Super Admin'
                ], 403);
            }

            // Only super admin can change admin roles
            if ($user->role === 'admin' && !$isSuperAdmin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền thực hiện'
                ], 403);
            }

            $request->validate([
                'role' => 'required|in:user,admin,author'
            ], [
                'role.required' => 'Trường role không được để trống',
                'role.in' => 'Giá trị không hợp lệ'
            ]);

            $user->role = $request->role;
        }

        // Check permissions
        if ($authUser->role === 'mod') {
            if ($user->role === 'admin' || $user->id === $authUser->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền thực hiện'
                ], 403);
            }
        }

        // Handle ban toggles
        $banTypes = ['login', 'comment', 'rate', 'read'];
        foreach ($banTypes as $type) {
            $field = "ban_$type";
            if ($request->has($field)) {
                $user->$field = $request->boolean($field);
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

            // Check if IP already banned
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
            // Remove all banned IPs for this user
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
            'admin' => User::where('active', 'active')->where('role', 'admin')->count(),
            'mod' => User::where('active', 'active')->where('role', 'mod')->count(),
            'user' => User::where('active', 'active')->where('role', 'user')->count(),
            'author' => User::where('active', 'active')->where('role', 'author')->count(),
        ];

        if ($authUser->role === 'mod') {
            $query->where('role', '!=', 'admin')->where('role', '!=', 'mod');
        }


        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // IP filter
        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        // Date filter
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

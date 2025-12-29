<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RateLimitViolation;
use App\Services\RateLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RateLimitController extends Controller
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    /**
     * Display list of users with rate limit violations
     * Chỉ hiển thị user có vi phạm (có record trong rate_limit_violations)
     */
    public function index(Request $request)
    {
        // Get users who have rate limit violations (có vi phạm mới hiển thị)
        $query = User::whereHas('rateLimitViolations')
            ->where('role', 'user') // Only regular users, not admins
            ->with(['userBan', 'rateLimitViolations' => function($q) {
                $q->orderBy('violated_at', 'desc');
            }]);

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by ban type
        if ($request->filled('ban_type')) {
            $banType = $request->ban_type;
            if ($banType === 'permanent') {
                $query->whereHas('userBan', function($q) {
                    $q->where('read', true);
                });
            } elseif ($banType === 'temporary') {
                $query->whereHas('userBan', function($q) {
                    $q->whereNotNull('read_banned_until')
                      ->where('read_banned_until', '>', now())
                      ->where('read', false);
                });
            } elseif ($banType === 'no_ban') {
                // User có vi phạm nhưng chưa bị ban
                $query->whereDoesntHave('userBan', function($q) {
                    $q->where('read', true)
                      ->orWhere(function($tempQ) {
                          $tempQ->whereNotNull('read_banned_until')
                                ->where('read_banned_until', '>', now());
                      });
                });
            }
        }

        $users = $query->paginate(20)->withQueryString();

        // Get violation counts for each user
        foreach ($users as $user) {
            $user->violation_count_today = $this->rateLimitService->getViolationCountToday($user);
            $user->total_violations = $user->rateLimitViolations->count();
            
            // Determine ban type
            $userBan = $user->userBan;
            if ($userBan && $userBan->read) {
                $user->ban_type = 'permanent';
                $user->banned_until = null;
            } elseif ($userBan && $userBan->read_banned_until && $userBan->read_banned_until->isFuture()) {
                $user->ban_type = 'temporary';
                $user->banned_until = $userBan->read_banned_until;
            } else {
                $user->ban_type = 'no_ban';
                $user->banned_until = null;
            }
        }

        return view('admin.pages.rate-limit.index', compact('users'));
    }

    /**
     * Unlock a user from rate limit ban
     */
    public function unlock($id)
    {
        $user = User::findOrFail($id);

        try {
            $unbanned = $this->rateLimitService->unbanUser($user);

            if ($unbanned) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Đã mở khóa tài khoản thành công'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tài khoản không bị khóa hoặc đã được mở khóa'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RateLimitViolation;
use App\Services\RateLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

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
        // Sắp xếp theo lần vi phạm gần nhất (violated_at mới nhất)
        // Hiển thị cả user và admin_sub (admin_main không bị rate limit nên không cần hiển thị)
        $query = User::whereHas('rateLimitViolations')
            ->whereIn('role', ['user', 'admin_sub'])
            ->with(['userBan', 'rateLimitViolations' => function($q) {
                $q->orderBy('violated_at', 'desc');
            }])
            ->addSelect([
                'latest_violation' => RateLimitViolation::select('violated_at')
                    ->whereColumn('user_id', 'users.id')
                    ->orderBy('violated_at', 'desc')
                    ->limit(1)
            ])
            ->orderBy('latest_violation', 'desc');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Filter by ban type (chỉ lọc ban từ rate limit)
        if ($request->filled('ban_type')) {
            $banType = $request->ban_type;
            if ($banType === 'permanent') {
                $query->whereHas('userBan', function($q) {
                    $q->where('rate_limit_ban', true)
                      ->where('read', true);
                });
            } elseif ($banType === 'temporary') {
                $query->whereHas('userBan', function($q) {
                    $q->where('rate_limit_ban', true)
                      ->whereNotNull('read_banned_until')
                      ->where('read_banned_until', '>', now())
                      ->where('read', false);
                });
            } elseif ($banType === 'no_ban') {
                // User có vi phạm nhưng chưa bị ban từ rate limit
                $query->whereDoesntHave('userBan', function($q) {
                    $q->where('rate_limit_ban', true)
                      ->where(function($subQ) {
                          $subQ->where('read', true)
                               ->orWhere(function($tempQ) {
                                   $tempQ->whereNotNull('read_banned_until')
                                         ->where('read_banned_until', '>', now());
                               });
                      });
                });
            }
        }

        // Get users first (before calculating counts for filtering)
        $users = $query->get();

        // Calculate counts for each user and apply filters
        $filteredUsers = [];
        foreach ($users as $user) {
            $user->violation_count_today = $this->rateLimitService->getViolationCountToday($user);
            $user->total_violations = $user->rateLimitViolations->count();
            
            // Tính toán vi phạm theo ngày (lịch sử)
            $violationsByDate = $user->rateLimitViolations
                ->groupBy(function($violation) {
                    return $violation->violated_at->format('Y-m-d');
                })
                ->map(function($group) {
                    return $group->count();
                })
                ->sortKeysDesc(); // Sắp xếp theo ngày mới nhất trước
            
            $user->violations_by_date = $violationsByDate;
            
            // Determine ban type (chỉ xét ban từ rate limit)
            $userBan = $user->userBan;
            if ($userBan && $userBan->rate_limit_ban && $userBan->read) {
                $user->ban_type = 'permanent';
                $user->banned_until = null;
            } elseif ($userBan && $userBan->rate_limit_ban && $userBan->read_banned_until && $userBan->read_banned_until->isFuture()) {
                $user->ban_type = 'temporary';
                $user->banned_until = $userBan->read_banned_until;
            } else {
                $user->ban_type = 'no_ban';
                $user->banned_until = null;
            }
            
            // Get ban counts
            $user->temp_ban_count = $userBan->temp_ban_count ?? 0;
            $user->permanent_ban_count = $userBan->permanent_ban_count ?? 0;
            
            // Apply filters
            $passFilter = true;
            
            if ($request->filled('violation_today_min')) {
                $min = (int) $request->violation_today_min;
                if ($user->violation_count_today < $min) {
                    $passFilter = false;
                }
            }
            
            if ($request->filled('total_violations_min') && $passFilter) {
                $min = (int) $request->total_violations_min;
                if ($user->total_violations < $min) {
                    $passFilter = false;
                }
            }
            
            if ($request->filled('temp_ban_count_min') && $passFilter) {
                $min = (int) $request->temp_ban_count_min;
                if ($user->temp_ban_count < $min) {
                    $passFilter = false;
                }
            }
            
            if ($request->filled('permanent_ban_count_min') && $passFilter) {
                $min = (int) $request->permanent_ban_count_min;
                if ($user->permanent_ban_count < $min) {
                    $passFilter = false;
                }
            }
            
            if ($passFilter) {
                $filteredUsers[] = $user;
            }
        }
        
        // Manual pagination
        $currentPage = request()->get('page', 1);
        $perPage = 20;
        $total = count($filteredUsers);
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($filteredUsers, $offset, $perPage);
        
        $users = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

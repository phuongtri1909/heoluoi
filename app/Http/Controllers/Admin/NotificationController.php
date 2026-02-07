<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Jobs\AttachNotificationRecipientsJob;

class NotificationController extends Controller
{
    /** Số user vượt ngưỡng này thì đẩy queue thay vì ghi đồng bộ */
    public const QUEUE_THRESHOLD = 100;

    public function index(Request $request)
    {
        $query = Notification::with('createdBy')->latest();

        if ($request->filled('type')) {
            if ($request->type === 'broadcast') {
                $query->where('is_broadcast', true);
            } elseif ($request->type === 'targeted') {
                $query->where('is_broadcast', false);
            }
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('admin.pages.notifications.index', compact('notifications'));
    }

    public function edit(Notification $notification)
    {
        return view('admin.pages.notifications.edit', compact('notification'));
    }

    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
        ], [], ['title' => 'Tiêu đề', 'body' => 'Nội dung']);

        $notification->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Đã cập nhật thông báo.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Đã xóa thông báo.');
    }

    public function create()
    {
        return view('admin.pages.notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'send_type' => 'required|in:all,selected,search_all',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
            'search_query' => 'nullable|string|max:255',
        ], [], [
            'title' => 'Tiêu đề',
            'body' => 'Nội dung',
            'send_type' => 'Loại gửi',
        ]);

        $sendType = $validated['send_type'];
        $userIds = [];

        if ($sendType === 'all') {
            $notification = Notification::create([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'is_broadcast' => true,
                'created_by' => Auth::id(),
            ]);
            return redirect()
                ->route('admin.notifications.index')
                ->with('success', 'Đã tạo thông báo gửi tất cả user.');
        }

        if ($sendType === 'selected') {
            $userIds = array_values(array_unique($validated['user_ids'] ?? []));
            if (empty($userIds)) {
                return back()->withErrors(['user_ids' => 'Vui lòng chọn ít nhất một người nhận.'])->withInput();
            }
        }

        if ($sendType === 'search_all') {
            $searchQuery = trim($validated['search_query'] ?? '');
            if ($searchQuery === '') {
                return back()->withErrors(['search_query' => 'Vui lòng nhập từ khóa tìm kiếm khi chọn "Chọn tất cả trong kết quả tìm kiếm".'])->withInput();
            }
            $userIds = $this->getUserIdsBySearch($searchQuery);
            if (empty($userIds)) {
                return back()->withErrors(['search_query' => 'Không tìm thấy user nào phù hợp.'])->withInput();
            }
        }

        $notification = Notification::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_broadcast' => false,
            'created_by' => Auth::id(),
        ]);

        $count = count($userIds);
        if ($count > self::QUEUE_THRESHOLD) {
            AttachNotificationRecipientsJob::dispatch($notification->id, $userIds);
            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "Đã tạo thông báo. Đang gửi cho {$count} user qua queue (xử lý nền).");
        }

        $this->attachRecipientsSync($notification->id, $userIds);
        return redirect()
            ->route('admin.notifications.index')
            ->with('success', "Đã tạo thông báo và gửi cho {$count} user.");
    }

    /**
     * API: Tìm user theo tên/email (không load hết).
     */
    public function usersSearch(Request $request)
    {
        $search = $request->get('q', '');
        $query = User::query()
            ->whereIn('role', [User::ROLE_USER]);

        if (strlen($search) >= 1) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->select('id', 'email', 'name')
            ->orderBy('name')
            ->limit(30)
            ->get();

        return response()->json($users);
    }

    /**
     * API: Đếm user theo từ khóa (để hiển thị "Chọn tất cả X user trong kết quả").
     */
    public function usersSearchCount(Request $request)
    {
        $search = trim($request->get('q', ''));
        $query = User::query()
            ->whereIn('role', [User::ROLE_USER]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        return response()->json(['count' => $query->count()]);
    }

    /**
     * API: Đếm + xem trước danh sách user sẽ nhận (gửi tất cả trong kết quả tìm kiếm).
     * Trả về count và tối đa 50 user đầu tiên để hiển thị "sẽ gửi cho những ai".
     */
    public function usersSearchPreview(Request $request)
    {
        $search = trim($request->get('q', ''));
        $query = User::query()
            ->whereIn('role', [User::ROLE_USER]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $count = $query->count();
        $users = (clone $query)
            ->select('id', 'email', 'name')
            ->orderBy('name')
            ->limit(50)
            ->get();

        return response()->json(['count' => $count, 'users' => $users]);
    }

    private function getUserIdsBySearch(string $search): array
    {
        return User::query()
            ->whereIn('role', [User::ROLE_USER])
            ->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            })
            ->pluck('id')
            ->toArray();
    }

    private function attachRecipientsSync(int $notificationId, array $userIds): void
    {
        $chunkSize = 500;
        foreach (array_chunk($userIds, $chunkSize) as $chunk) {
            $rows = collect($chunk)->map(fn ($userId) => [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();
            DB::table('notification_user')->insertOrIgnore($rows);
        }
    }
}

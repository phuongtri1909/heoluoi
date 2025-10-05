<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanIp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BanIpController extends Controller
{
    /**
     * Display a listing of banned IPs
     */
    public function index(Request $request)
    {
        $query = BanIp::with('user');

        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%')
                  ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }

        $banIps = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => BanIp::count(),
            'with_user' => BanIp::whereNotNull('user_id')->count(),
            'without_user' => BanIp::whereNull('user_id')->count(),
        ];

        return view('admin.pages.ban-ips.index', compact('banIps', 'stats'));
    }

    /**
     * Show the form for creating a new banned IP
     */
    public function create()
    {
        $users = User::where('active', 'active')->orderBy('name')->get();
        return view('admin.pages.ban-ips.create', compact('users'));
    }

    /**
     * Store a newly created banned IP
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'user_id' => 'nullable|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ],[
            'ip_address.required' => 'IP không được để trống',
            'ip_address.ip' => 'IP không hợp lệ',
            'user_id.exists' => 'Người dùng không tồn tại',
            'reason.string' => 'Lý do không hợp lệ',
            'reason.max' => 'Lý do không được vượt quá 255 ký tự'
        ]);

        // Check if IP is already banned
        if (BanIp::where('ip_address', $request->ip_address)->exists()) {
            return redirect()->back()->with('error', 'IP này đã bị cấm trước đó.');
        }

        BanIp::create([
            'ip_address' => $request->ip_address,
            'user_id' => $request->user_id,
            'reason' => $request->reason,
            'banned_by' => Auth::id(),
            'banned_at' => now()
        ]);

        return redirect()->route('admin.ban-ips.index')->with('success', 'Đã cấm IP thành công.');
    }

    /**
     * Show the form for editing a banned IP
     */
    public function edit(BanIp $banIp)
    {
        $users = User::where('active', 'active')->orderBy('name')->get();
        return view('admin.pages.ban-ips.edit', compact('banIp', 'users'));
    }

    /**
     * Update the specified banned IP
     */
    public function update(Request $request, BanIp $banIp)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'user_id' => 'nullable|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        // Check if IP is already banned by another record
        if (BanIp::where('ip_address', $request->ip_address)->where('id', '!=', $banIp->id)->exists()) {
            return redirect()->back()->with('error', 'IP này đã bị cấm bởi bản ghi khác.');
        }

        $banIp->update([
            'ip_address' => $request->ip_address,
            'user_id' => $request->user_id,
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.ban-ips.index')->with('success', 'Đã cập nhật thông tin cấm IP.');
    }

    /**
     * Remove the specified banned IP
     */
    public function destroy(BanIp $banIp)
    {
        $banIp->delete();
        return redirect()->route('admin.ban-ips.index')->with('success', 'Đã gỡ cấm IP thành công.');
    }

    /**
     * Ban IP from user show page
     */
    public function banUserIp(Request $request, User $user)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255'
        ]);

        // Check if IP is already banned
        if (BanIp::where('ip_address', $request->ip_address)->exists()) {
            return redirect()->back()->with('error', 'IP này đã bị cấm trước đó.');
        }

        BanIp::create([
            'ip_address' => $request->ip_address,
            'user_id' => $user->id,
            'reason' => $request->reason,
            'banned_by' => Auth::id(),
            'banned_at' => now()
        ]);

        return redirect()->back()->with('success', 'Đã cấm IP của người dùng thành công.');
    }
}

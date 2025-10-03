<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\CoinTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CoinTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = CoinTransfer::with(['fromAdmin', 'toUser']);


        if (Auth::user()->role === 'admin_sub') {
            $query->where('from_admin_id', Auth::id());
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('toUser', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('admin_id') && $request->admin_id && Auth::user()->role === 'admin_main') {
            $query->where('from_admin_id', $request->admin_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [];
        if (Auth::user()->role === 'admin_main') {
            $stats = [
                'total_transfers' => CoinTransfer::count(),
                'total_amount_transferred' => CoinTransfer::completed()->sum('amount'),
                'pending_transfers' => CoinTransfer::pending()->count(),
                'today_transfers' => CoinTransfer::whereDate('created_at', today())->count(),
                'admins_count' => CoinTransfer::distinct('from_admin_id')->count('from_admin_id'),
            ];
        }

        $admins = collect();
        if (Auth::user()->role === 'admin_main') {
            $admins = User::where('role', 'admin_sub')
                ->join('coin_transfers', 'users.id', '=', 'coin_transfers.from_admin_id')
                ->distinct()
                ->select('users.id', 'users.name', 'users.email')
                ->get();
        }

        return view('admin.pages.coin-transfers.index', compact('transfers', 'stats', 'admins'));
    }

    /**
     * Show form to create new transfer (admin_sub only)
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin_sub') {
            abort(403, 'Chỉ admin_sub mới được phép chuyển cám');
        }

        return view('admin.pages.coin-transfers.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin_sub') {
            abort(403, 'Chỉ admin_sub mới được phép chuyển cám');
        }

        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'amount' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
        ], [
            'user_ids.required' => 'Vui lòng chọn người nhận',
            'user_ids.array' => 'Người nhận không hợp lệ',
            'user_ids.min' => 'Vui lòng chọn ít nhất 1 người nhận',
            'user_ids.*.exists' => 'Một trong số người nhận không tồn tại',
            'amount.required' => 'Vui lòng nhập số Cám',
            'amount.integer' => 'Số Cám phải là số nguyên',
            'amount.min' => 'Số Cám phải lớn hơn 0',
            'amount.max' => 'Số Cám không được vượt quá 50,000',
            'note.max' => 'Ghi chú không được vượt quá 500 ký tự',
        ]);

        $admin = Auth::user();
        $recipients = User::whereIn('id', $request->user_ids)->where('role', 'user')->get();

        if ($recipients->count() !== count($request->user_ids)) {
            return redirect()->back()->withErrors(['user_ids' => 'Một trong số người nhận không tồn tại']);
        }

        $totalAmountNeeded = $request->amount * count($request->user_ids);

        if ($admin->coins < $totalAmountNeeded) {
            return redirect()->back()->withErrors(['amount' => "Số Cám của bạn ({$admin->coins}) không đủ để chuyển {$totalAmountNeeded} Cám cho " . count($request->user_ids) . " người"]);
        }

        DB::beginTransaction();

        try {
            $transfers = [];
            $recipientEmails = [];

            foreach ($recipients as $recipient) {
                $transfer = CoinTransfer::create([
                    'from_admin_id' => $admin->id,
                    'to_user_id' => $recipient->id,
                    'amount' => $request->amount,
                    'note' => $request->note,
                    'status' => 'completed',
                ]);

                $transfers[] = $transfer;
                $recipientEmails[] = $recipient->email;

                $recipient->coins += $request->amount;
                $recipient->save();
            }

            $admin->coins -= $totalAmountNeeded;
            $admin->save();

            DB::commit();

            $message = count($transfers) === 1
                ? "Đã chuyển {$request->amount} Cám cho {$recipientEmails[0]} thành công"
                : "Đã chuyển {$request->amount} Cám cho " . count($transfers) . " người thành công";

            return redirect()->route('admin.coin-transfers.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing coin transfer: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý chuyển Cám');
        }
    }

    /**
     * Get user suggestions for autocomplete
     */
    public function getUserSuggestions(Request $request)
    {
        $query = $request->q;

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('role', 'user')
            ->where(function ($q) use ($query) {
                $q->where('email', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('id', 'email', 'name', 'coins')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Show transfer details
     */
    public function show(CoinTransfer $transfer)
    {
        if (Auth::user()->role === 'admin_sub' && $transfer->from_admin_id !== Auth::id()) {
            abort(403, 'Bạn chỉ được xem các chuyển khoản của mình');
        }

        return view('admin.pages.coin-transfers.show', compact('transfer'));
    }

    /**
     * Dashboard API for admin_main to get transfer stats
     */
    public function statsApi(Request $request)
    {
        if (Auth::user()->role !== 'admin_main') {
            abort(403, 'Chỉ admin_main mới được truy cập');
        }

        $stats = [
            'total_transfers' => CoinTransfer::count(),
            'total_amount' => CoinTransfer::completed()->sum('amount'),
            'pending_transfers' => CoinTransfer::pending()->count(),
            'today_transfers' => CoinTransfer::whereDate('created_at', today())->count(),
            'recent_transfers' => CoinTransfer::with(['fromAdmin', 'toUser'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($transfer) {
                    return [
                        'id' => $transfer->id,
                        'amount' => number_format($transfer->amount),
                        'from_admin' => $transfer->fromAdmin->name,
                        'to_user' => $transfer->toUser->email,
                        'created_at' => $transfer->created_at->format('d/m/Y H:i'),
                    ];
                }),
        ];

        return response()->json($stats);
    }
}

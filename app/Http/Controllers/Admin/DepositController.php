<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Config;
use App\Models\Deposit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{

    public $coinBankPercent;
    public $coinPayPalPercent;
    public $coinCardPercent;

    public $coinExchangeRate;
    public $coinPayPalRate;

    public function __construct()
    {
        $this->coinBankPercent = Config::getConfig('coin_bank_percentage', 15);
        $this->coinPayPalPercent = Config::getConfig('coin_paypal_percentage', 0);
        $this->coinCardPercent = Config::getConfig('coin_card_percentage', 30);

        $this->coinExchangeRate = Config::getConfig('coin_exchange_rate', 100);
        $this->coinPayPalRate = Config::getConfig('coin_paypal_rate', 20000);
    }

    // Xử lý phê duyệt giao dịch (cho admin)
    public function approve(Deposit $deposit)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin_main') {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này.');
        }

        DB::beginTransaction();
        try {
            if ($deposit->status !== Deposit::STATUS_PENDING) {
                return redirect()->back()->with('error', 'Giao dịch đã được xử lý trước đó.');
            }
            
            $deposit->update([
                'status' => Deposit::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

           
            $user = $deposit->user;
            
            // Sử dụng CoinService để ghi lịch sử
            $coinService = new \App\Services\CoinService();
            $coinService->addCoins(
                $user,
                $deposit->total_coins ?? $deposit->coins,
                \App\Models\CoinHistory::TYPE_BANK_DEPOSIT,
                "Nạp chuyển khoản thành công - Số tiền: " . number_format($deposit->amount) . " VND",
                $deposit
            );

            DB::commit();

            return redirect()->back()->with('success', 'Đã phê duyệt giao dịch và cộng ' . ($deposit->total_coins ?? $deposit->coins) . ' cám vào tài khoản người dùng.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving deposit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi phê duyệt giao dịch');
        }
    }

    // Xử lý từ chối giao dịch (cho admin)
    public function reject(Request $request, Deposit $deposit)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin_main') {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện chức năng này.');
        }

        $request->validate([
            'note' => 'required',
        ], [
            'note.required' => 'Vui lòng nhập lý do từ chối',
        ]);

        try {

            if ($deposit->status !== Deposit::STATUS_PENDING) {
                return redirect()->back()->with('error', 'Giao dịch đã được xử lý trước đó.');
            }

            $deposit->update([
                'status' => Deposit::STATUS_REJECTED,
                'note' => $request->note,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Đã từ chối giao dịch.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting deposit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi từ chối giao dịch');
        }
    }

    // Hiển thị trang quản lý giao dịch (cho admin)
    public function adminIndex(Request $request)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin_main') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $query = Deposit::with(['user', 'bank']);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $deposits = $query->latest()->paginate(15);

        return view('admin.pages.deposits.index', compact('deposits'));
    }
}

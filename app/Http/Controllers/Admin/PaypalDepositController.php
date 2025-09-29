<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\PaypalDeposit;
use App\Models\RequestPaymentPaypal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaypalDepositController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = PaypalDeposit::with(['user', 'requestPaymentPaypal']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $paypalDeposits = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.deposits.paypal.index', compact('paypalDeposits'));
    }

    /**
     * Admin: Approve PayPal deposit
     */
    public function approve(PaypalDeposit $deposit)
    {
        if ($deposit->status !== PaypalDeposit::STATUS_PROCESSING) {
            return back()->with('error', 'Chỉ có thể duyệt giao dịch đang xử lý');
        }

        try {
            DB::beginTransaction();

            $deposit->markAsApproved('Đã duyệt bởi admin', null);

            DB::commit();

            return back()->with('success', 'Đã duyệt giao dịch PayPal thành công. Xu đã được cộng vào tài khoản người dùng.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Reject PayPal deposit
     */
    public function reject(Request $request, PaypalDeposit $deposit)
    {
        $request->validate([
            'note' => 'required|string|max:500'
        ], [
            'note.required' => 'Vui lòng nhập lý do từ chối'
        ]);

        if ($deposit->status !== PaypalDeposit::STATUS_PROCESSING) {
            return back()->with('error', 'Chỉ có thể từ chối giao dịch đang xử lý');
        }

        try {
            $deposit->markAsRejected($request->note);
            return back()->with('success', 'Đã từ chối giao dịch PayPal');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Display PayPal request payments management
     */
    public function requestPaymentIndex(Request $request)
    {
        $query = RequestPaymentPaypal::with(['user'])
            ->where('payment_type', RequestPaymentPaypal::TYPE_PAYPAL);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->where('expired_at', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('paypal_email', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $requestPayments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.deposits.paypal.request_payments', compact('requestPayments'));
    }

    /**
     * Admin: Delete expired PayPal request payments
     */
    public function deleteExpiredRequests()
    {
        try {
            $deleted = RequestPaymentPaypal::where('payment_type', RequestPaymentPaypal::TYPE_PAYPAL)
                ->where('status', RequestPaymentPaypal::STATUS_PENDING)
                ->where('expired_at', '<', now())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deleted} yêu cầu thanh toán hết hạn"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

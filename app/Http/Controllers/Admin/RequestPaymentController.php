<?php

namespace App\Http\Controllers\Admin;

use App\Models\RequestPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class RequestPaymentController extends Controller
{

    public function adminIndex(Request $request)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin_main') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $query = RequestPayment::with(['user', 'bank', 'deposit']);

        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'expired') {
                $query->whereNotNull('expired_at')->where('expired_at', '<', now())->where('is_completed', false);
            } elseif ($request->status === 'pending') {
                $query->where('is_completed', false)->where(function ($q) {
                    $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
                });
            } elseif ($request->status === 'completed') {
                $query->where('is_completed', true);
            }
        }

        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $requestPayments = $query->latest()->paginate(15);

        return view('admin.pages.deposits.request_payment.index', compact('requestPayments'));
    }

    public function deleteExpired()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin_main') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này.'
            ], 403);
        }

        $count = RequestPayment::whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->where('is_completed', false)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$count} yêu cầu thanh toán hết hạn."
        ]);
    }
}

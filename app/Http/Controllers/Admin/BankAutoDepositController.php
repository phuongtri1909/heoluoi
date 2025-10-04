<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAutoDeposit;
use App\Models\BankAuto;
use App\Models\User;
use App\Services\CoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BankAutoDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = BankAutoDeposit::with(['user', 'bankAuto']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by transaction code or user
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('username', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $deposits = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => BankAutoDeposit::count(),
            'pending' => BankAutoDeposit::where('status', 'pending')->count(),
            'success' => BankAutoDeposit::where('status', 'success')->count(),
            'failed' => BankAutoDeposit::where('status', 'failed')->count(),
            'total_amount' => BankAutoDeposit::where('status', 'success')->sum('amount'),
            'total_coins' => BankAutoDeposit::where('status', 'success')->sum('total_coins'),
        ];

        return view('admin.pages.bank-auto-deposits.index', compact('deposits', 'stats'));
    }

    public function show(BankAutoDeposit $bankAutoDeposit)
    {
        $bankAutoDeposit->load(['user', 'bankAuto']);
        return view('admin.pages.bank-auto-deposits.show', compact('bankAutoDeposit'));
    }
}

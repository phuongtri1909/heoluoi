<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CoinService;
use App\Models\User;
use Illuminate\Http\Request;

class CoinHistoryController extends Controller
{
    protected $coinService;

    public function __construct(CoinService $coinService)
    {
        $this->coinService = $coinService;
    }

    /**
     * Display all coin transactions
     */
    public function index(Request $request)
    {
        $query = \App\Models\CoinHistory::with(['user', 'reference']);

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = $this->coinService->getAdminStats($request->date_from, $request->date_to);

        // Get users for filter
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        // Get transaction types for filter
        $transactionTypes = [
            \App\Models\CoinHistory::TYPE_CARD_DEPOSIT => 'Nạp thẻ',
            \App\Models\CoinHistory::TYPE_PAYPAL_DEPOSIT => 'Nạp PayPal',
            \App\Models\CoinHistory::TYPE_BANK_DEPOSIT => 'Nạp chuyển khoản',
            \App\Models\CoinHistory::TYPE_CHAPTER_PURCHASE => 'Mua chương',
            \App\Models\CoinHistory::TYPE_STORY_PURCHASE => 'Mua combo truyện',
            \App\Models\CoinHistory::TYPE_CHAPTER_EARNINGS => 'Thu nhập từ chương',
            \App\Models\CoinHistory::TYPE_STORY_EARNINGS => 'Thu nhập từ truyện',
            \App\Models\CoinHistory::TYPE_FEATURED_STORY => 'Đề cử truyện',
            \App\Models\CoinHistory::TYPE_WITHDRAWAL => 'Rút tiền',
            \App\Models\CoinHistory::TYPE_WITHDRAWAL_REFUND => 'Hoàn tiền rút',
            \App\Models\CoinHistory::TYPE_DAILY_TASK => 'Nhiệm vụ hàng ngày',
            \App\Models\CoinHistory::TYPE_REFUND => 'Hoàn tiền',
            \App\Models\CoinHistory::TYPE_BONUS => 'Thưởng',
        ];

        return view('admin.pages.coin-history.index', compact(
            'transactions',
            'stats',
            'users',
            'transactionTypes'
        ));
    }

    /**
     * Show user's coin history
     */
    public function showUser($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        
        $filters = [
            'type' => $request->get('type'),
            'transaction_type' => $request->get('transaction_type'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'per_page' => 20,
        ];

        $transactions = $this->coinService->getUserTransactions($user, $filters);
        $stats = $this->coinService->getUserStats($user, $filters['date_from'], $filters['date_to']);

        return view('admin.pages.coin-history.user', compact('user', 'transactions', 'stats', 'filters'));
    }
}

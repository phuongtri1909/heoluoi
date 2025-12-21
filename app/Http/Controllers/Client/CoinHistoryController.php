<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CoinService;
use Illuminate\Http\Request;

class CoinHistoryController extends Controller
{
    protected $coinService;

    public function __construct(CoinService $coinService)
    {
        $this->coinService = $coinService;
    }

    /**
     * Display user's coin history
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $filters = [
            'type' => $request->get('type'),
            'transaction_type' => $request->get('transaction_type'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'per_page' => 10,
        ];

        $transactions = $this->coinService->getUserTransactions($user, $filters);

        $stats = $this->coinService->getUserStats($user, $filters['date_from'], $filters['date_to']);

        $transactionTypes = [
            \App\Models\CoinHistory::TYPE_CARD_DEPOSIT => 'Nạp thẻ',
            \App\Models\CoinHistory::TYPE_PAYPAL_DEPOSIT => 'Nạp PayPal',
            \App\Models\CoinHistory::TYPE_BANK_DEPOSIT => 'Nạp chuyển khoản',
            \App\Models\CoinHistory::TYPE_BANK_AUTO_DEPOSIT => 'Nạp bank auto',
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

        return view('pages.information.user.coin_history', compact(
            'transactions',
            'stats',
            'transactionTypes',
            'filters'
        ));
    }
}

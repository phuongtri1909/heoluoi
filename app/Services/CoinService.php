<?php

namespace App\Services;

use App\Models\User;
use App\Models\CoinHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoinService
{
    /**
     * Add coins to user with transaction record
     */
    public function addCoins(User $user, int $amount, string $transactionType, string $description = null, $reference = null, $adminId = null)
    {
        return $this->processTransaction($user, $amount, 'add', $transactionType, $description, $reference, $adminId);
    }

    /**
     * Subtract coins from user with transaction record
     */
    public function subtractCoins(User $user, int $amount, string $transactionType, string $description = null, $reference = null, $adminId = null)
    {
        if ($user->coins < $amount) {
            throw new \Exception('Không đủ xu để thực hiện giao dịch này');
        }

        return $this->processTransaction($user, -$amount, 'subtract', $transactionType, $description, $reference, $adminId);
    }

    /**
     * Process coin transaction with full logging
     */
    protected function processTransaction(User $user, int $amount, string $type, string $transactionType, string $description = null, $reference = null, $adminId = null)
    {
        DB::beginTransaction();

        try {
            $balanceBefore = $user->coins;
            $balanceAfter = $balanceBefore + $amount;

            $user->coins = $balanceAfter;
            $user->save();

            $transaction = CoinHistory::create([
                'user_id' => $user->id,
                'amount' => abs($amount),
                'type' => $type,
                'transaction_type' => $transactionType,
                'description' => $description,
                'reference_id' => $reference ? $reference->id : null,
                'reference_type' => $reference ? get_class($reference) : null,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Coin transaction failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'transaction_type' => $transactionType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Transfer coins between users (for purchases)
     */
    public function transferCoins(User $fromUser, User $toUser, int $amount, string $transactionType, string $description = null, $reference = null)
    {
        DB::beginTransaction();

        try {
            // Subtract from buyer
            $buyerTransaction = $this->subtractCoins($fromUser, $amount, $transactionType, $description, $reference);

            // Add to seller
            $sellerTransaction = $this->addCoins($toUser, $amount, $this->getEarningsType($transactionType), $description, $reference);

            DB::commit();

            return [
                'buyer_transaction' => $buyerTransaction,
                'seller_transaction' => $sellerTransaction,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get earnings transaction type
     */
    protected function getEarningsType($purchaseType)
    {
        $mapping = [
            CoinHistory::TYPE_CHAPTER_PURCHASE => CoinHistory::TYPE_CHAPTER_EARNINGS,
            CoinHistory::TYPE_STORY_PURCHASE => CoinHistory::TYPE_STORY_EARNINGS,
        ];

        return $mapping[$purchaseType] ?? 'earnings';
    }

    /**
     * Get user transaction history
     */
    public function getUserTransactions(User $user, $filters = [])
    {
        $query = $user->coinHistories()->with(['reference']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get transaction statistics for user
     */
    public function getUserStats(User $user, $dateFrom = null, $dateTo = null)
    {
        $query = $user->coinHistories();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total_added' => (clone $query)->where('type', 'add')->sum('amount'),
            'total_subtracted' => (clone $query)->where('type', 'subtract')->sum('amount'),
            'total_transactions' => (clone $query)->count(),
            'by_type' => (clone $query)->selectRaw('transaction_type, type, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('transaction_type', 'type')
                ->get(),
        ];
    }

    /**
     * Get admin transaction statistics
     */
    public function getAdminStats($dateFrom = null, $dateTo = null)
    {
        $query = CoinHistory::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total_added' => (clone $query)->where('type', 'add')->sum('amount'),
            'total_subtracted' => (clone $query)->where('type', 'subtract')->sum('amount'),
            'total_transactions' => (clone $query)->count(),
            'by_type' => (clone $query)->selectRaw('transaction_type, type, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('transaction_type', 'type')
                ->get(),
            'by_user' => (clone $query)->with('user')
                ->selectRaw('user_id, type, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('user_id', 'type')
                ->get(),
        ];
    }
}

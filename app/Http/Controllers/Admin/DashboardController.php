<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\OnlineUser;
use App\Models\VisitorStat;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // // Default to current year, no month filter to show all data
        // $year = $request->get('year', date('Y'));
        // $month = $request->get('month', null); // No default month
        // $day = $request->get('day', null);
        
        // // Build date filter
        // $dateFilter = $this->buildDateFilter($year, $month, $day);
        
        // // Get basic stats
        // $basicStats = $this->getBasicStats($dateFilter);
        
        // // Get story view statistics
        // $storyViews = $this->getStoryViewStats($dateFilter);
        
        // // Get revenue statistics
        // $revenueStats = $this->getRevenueStats($dateFilter);
        
        // // Get coin statistics
        // $coinStats = $this->getCoinStats($dateFilter);
        
        // // Get deposit statistics
        // $depositStats = $this->getDepositStats($dateFilter);
        
        // // Get withdrawal statistics
        // $withdrawalStats = $this->getWithdrawalStats($dateFilter);
        
        // // Get daily task statistics
        // $dailyTaskStats = $this->getDailyTaskStats($dateFilter);
        
        // // Get author revenue statistics
        // $authorRevenueStats = $this->getAuthorRevenueStats($dateFilter);
        
        // // Get manual coin transaction statistics
        // $manualCoinStats = $this->getManualCoinStats($dateFilter);
        
        // // Get visitor statistics
        // $visitorStats = $this->getVisitorStats($dateFilter);
        
        // // Get online users statistics
        // $onlineStats = $this->getOnlineStats();
        
        // return view('admin.pages.dashboard', compact(
        //     'basicStats',
        //     'storyViews',
        //     'revenueStats',
        //     'coinStats',
        //     'depositStats',
        //     'withdrawalStats',
        //     'dailyTaskStats',
        //     'authorRevenueStats',
        //     'manualCoinStats',
        //     'visitorStats',
        //     'onlineStats',
        //     'year',
        //     'month',
        //     'day'
        // ));

        return view('admin.pages.dashboard');
    }
    
    public function getStatsData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $day = $request->get('day', null);
        
        $dateFilter = $this->buildDateFilter($year, $month, $day);
        
        return response()->json([
            'basicStats' => $this->getBasicStats($dateFilter),
            'storyViews' => $this->getStoryViewStats($dateFilter),
            'revenueStats' => $this->getRevenueStats($dateFilter),
            'coinStats' => $this->getCoinStats($dateFilter),
            'depositStats' => $this->getDepositStats($dateFilter),
            'withdrawalStats' => $this->getWithdrawalStats($dateFilter),
            'dailyTaskStats' => $this->getDailyTaskStats($dateFilter),
            'authorRevenueStats' => $this->getAuthorRevenueStats($dateFilter),
            'manualCoinStats' => $this->getManualCoinStats($dateFilter),
            'visitorStats' => $this->getVisitorStats($dateFilter),
            'onlineStats' => $this->getOnlineStats(),
        ]);
    }
    
    private function buildDateFilter($year, $month, $day = null)
    {
        if ($day) {
            return [
                'start' => Carbon::create($year, $month, $day)->startOfDay(),
                'end' => Carbon::create($year, $month, $day)->endOfDay(),
                'type' => 'day'
            ];
        } elseif ($month) {
            return [
                'start' => Carbon::create($year, $month, 1)->startOfMonth(),
                'end' => Carbon::create($year, $month, 1)->endOfMonth(),
                'type' => 'month'
            ];
        } else {
            return [
                'start' => Carbon::create($year, 1, 1)->startOfYear(),
                'end' => Carbon::create($year, 12, 31)->endOfYear(),
                'type' => 'year'
            ];
        }
    }
    
    private function getBasicStats($dateFilter)
    {
        // Single optimized query for basic stats
        $stats = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?) as new_users,
                (SELECT COUNT(*) FROM stories WHERE created_at BETWEEN ? AND ?) as new_stories,
                (SELECT COUNT(*) FROM chapters WHERE created_at BETWEEN ? AND ?) as new_chapters,
                (SELECT COUNT(*) FROM comments WHERE created_at BETWEEN ? AND ?) as new_comments,
                (SELECT COUNT(*) FROM users WHERE active = 'active') as total_active_users,
                (SELECT COUNT(*) FROM stories WHERE status = 'published') as total_published_stories,
                (SELECT COUNT(*) FROM chapters WHERE status = 'published') as total_published_chapters
        ", [
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end']
        ])[0];
        
        return (array) $stats;
    }
    
    private function getStoryViewStats($dateFilter)
    {
        // Get story views with total views calculated from chapters
        $storyViews = DB::select("
            SELECT 
                s.id,
                s.title,
                s.slug,
                s.author_name,
                COALESCE(SUM(c.views), 0) as total_views,
                COALESCE(SUM(c.views), 0) as chapter_views,
                COUNT(c.id) as chapter_count,
                s.created_at
            FROM stories s
            LEFT JOIN chapters c ON s.id = c.story_id
            WHERE s.status = 'published'
            GROUP BY s.id, s.title, s.slug, s.author_name, s.created_at
            ORDER BY total_views DESC
            LIMIT 20
        ");
        
        return $storyViews;
    }
    
    private function getRevenueStats($dateFilter)
    {
        // Get revenue by story and chapter
        $revenueStats = DB::select("
            SELECT 
                'story' as type,
                s.id,
                s.title,
                s.author_name,
                COUNT(sp.id) as purchase_count,
                COALESCE(SUM(sp.amount_paid), 0) as total_revenue,
                COALESCE(SUM(sp.amount_received), 0) as author_revenue
            FROM stories s
            LEFT JOIN story_purchases sp ON s.id = sp.story_id 
                AND sp.created_at BETWEEN ? AND ?
            WHERE s.status = 'published'
            GROUP BY s.id, s.title, s.author_name
            HAVING total_revenue > 0
            
            UNION ALL
            
            SELECT 
                'chapter' as type,
                s.id,
                s.title,
                s.author_name,
                COUNT(cp.id) as purchase_count,
                COALESCE(SUM(cp.amount_paid), 0) as total_revenue,
                COALESCE(SUM(cp.amount_received), 0) as author_revenue
            FROM stories s
            LEFT JOIN chapters c ON s.id = c.story_id
            LEFT JOIN chapter_purchases cp ON c.id = cp.chapter_id 
                AND cp.created_at BETWEEN ? AND ?
            WHERE s.status = 'published'
            GROUP BY s.id, s.title, s.author_name
            HAVING total_revenue > 0
            
            ORDER BY total_revenue DESC
            LIMIT 20
        ", [
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end']
        ]);
        
        return $revenueStats;
    }
    
    private function getCoinStats($dateFilter)
    {
        // Get total coin statistics
        $coinStats = DB::select("
            SELECT 
                (SELECT COALESCE(SUM(coins), 0) FROM users WHERE active = 'active') as total_user_coins,
                (
                    (SELECT COALESCE(SUM(coins), 0) FROM deposits WHERE status = 'approved' AND created_at BETWEEN ? AND ?) +
                    (SELECT COALESCE(SUM(coins), 0) FROM paypal_deposits WHERE status = 'approved' AND created_at BETWEEN ? AND ?) +
                    (SELECT COALESCE(SUM(coins), 0) FROM card_deposits WHERE status = 'success' AND created_at BETWEEN ? AND ?)
                ) as total_deposited,
                (SELECT COALESCE(SUM(coins), 0) FROM withdrawal_requests WHERE status = 'approved' AND created_at BETWEEN ? AND ?) as total_withdrawn,
                (SELECT COALESCE(SUM(udt.coin_reward * udt.completed_count), 0) FROM user_daily_tasks udt WHERE udt.created_at BETWEEN ? AND ?) as total_daily_task_coins,
                (SELECT COALESCE(SUM(amount), 0) FROM coin_transactions WHERE type = 'add' AND created_at BETWEEN ? AND ?) as total_manual_added,
                (SELECT COALESCE(SUM(amount), 0) FROM coin_transactions WHERE type = 'subtract' AND created_at BETWEEN ? AND ?) as total_manual_subtracted
        ", [
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end']
        ])[0];
        
        return (array) $coinStats;
    }
    
    private function getDepositStats($dateFilter)
    {
        // Get deposit statistics by type
        $depositStats = DB::select("
            SELECT 
                'bank' as type,
                COUNT(*) as count,
                COALESCE(SUM(coins), 0) as total_amount,
                COALESCE(AVG(coins), 0) as avg_amount
            FROM deposits 
            WHERE status = 'approved' AND created_at BETWEEN ? AND ?
            
            UNION ALL
            
            SELECT 
                'paypal' as type,
                COUNT(*) as count,
                COALESCE(SUM(coins), 0) as total_amount,
                COALESCE(AVG(coins), 0) as avg_amount
            FROM paypal_deposits 
            WHERE status = 'approved' AND created_at BETWEEN ? AND ?
            
            UNION ALL
            
            SELECT 
                'card' as type,
                COUNT(*) as count,
                COALESCE(SUM(coins), 0) as total_amount,
                COALESCE(AVG(coins), 0) as avg_amount
            FROM card_deposits 
            WHERE status = 'success' AND created_at BETWEEN ? AND ?
        ", [
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end']
        ]);
        
        return $depositStats;
    }
    
    private function getWithdrawalStats($dateFilter)
    {
        // Get withdrawal statistics
        $withdrawalStats = DB::select("
            SELECT 
                status,
                COUNT(*) as count,
                COALESCE(SUM(coins), 0) as total_amount,
                COALESCE(AVG(coins), 0) as avg_amount
            FROM withdrawal_requests 
            WHERE created_at BETWEEN ? AND ?
            GROUP BY status
            ORDER BY 
                CASE status 
                    WHEN 'pending' THEN 1 
                    WHEN 'approved' THEN 2 
                    WHEN 'rejected' THEN 3 
                END
        ", [
            $dateFilter['start'], $dateFilter['end']
        ]);
        
        return $withdrawalStats;
    }
    
    private function getDailyTaskStats($dateFilter)
    {
        // Get daily task statistics
        $dailyTaskStats = DB::select("
            SELECT 
                dt.name,
                dt.type,
                COUNT(udt.id) as completion_count,
                COALESCE(AVG(udt.coin_reward), 0) as avg_coins_per_task,
                COALESCE(SUM(udt.coin_reward * udt.completed_count), 0) as total_coins_distributed
            FROM daily_tasks dt
            LEFT JOIN user_daily_tasks udt ON dt.id = udt.daily_task_id 
                AND udt.created_at BETWEEN ? AND ?
            WHERE dt.active = 1
            GROUP BY dt.id, dt.name, dt.type
            ORDER BY completion_count DESC
        ", [
            $dateFilter['start'], $dateFilter['end']
        ]);
        
        return $dailyTaskStats;
    }
    
    private function getAuthorRevenueStats($dateFilter)
    {
        // Get author revenue statistics - Fixed to avoid duplicate counting
        $authorRevenueStats = DB::select("
            SELECT 
                u.id,
                u.name,
                u.email,
                COUNT(DISTINCT s.id) as story_count,
                COUNT(DISTINCT c.id) as chapter_count,
                COALESCE(story_revenue.total, 0) as story_revenue,
                COALESCE(chapter_revenue.total, 0) as chapter_revenue,
                COALESCE(story_revenue.total, 0) + COALESCE(chapter_revenue.total, 0) as total_revenue
            FROM users u
            LEFT JOIN stories s ON u.id = s.user_id AND s.status = 'published'
            LEFT JOIN chapters c ON s.id = c.story_id
            LEFT JOIN (
                SELECT 
                    s2.user_id,
                    SUM(sp.amount_received) as total
                FROM stories s2
                INNER JOIN story_purchases sp ON s2.id = sp.story_id 
                    AND sp.created_at BETWEEN ? AND ?
                WHERE s2.status = 'published'
                GROUP BY s2.user_id
            ) as story_revenue ON u.id = story_revenue.user_id
            LEFT JOIN (
                SELECT 
                    s3.user_id,
                    SUM(cp.amount_received) as total
                FROM stories s3
                INNER JOIN chapters c2 ON s3.id = c2.story_id
                INNER JOIN chapter_purchases cp ON c2.id = cp.chapter_id 
                    AND cp.created_at BETWEEN ? AND ?
                WHERE s3.status = 'published'
                GROUP BY s3.user_id
            ) as chapter_revenue ON u.id = chapter_revenue.user_id
            WHERE u.role = 'author' AND u.active = 'active'
            GROUP BY u.id, u.name, u.email, story_revenue.total, chapter_revenue.total
            HAVING total_revenue > 0
            ORDER BY total_revenue DESC
            LIMIT 20
        ", [
            $dateFilter['start'], $dateFilter['end'],
            $dateFilter['start'], $dateFilter['end']
        ]);
        
        return $authorRevenueStats;
    }
    
    private function getManualCoinStats($dateFilter)
    {
        // Get manual coin transaction statistics
        $manualCoinStats = DB::select("
            SELECT 
                ct.type,
                COUNT(*) as transaction_count,
                COALESCE(SUM(ct.amount), 0) as total_amount,
                COALESCE(AVG(ct.amount), 0) as avg_amount,
                u.name as admin_name
            FROM coin_transactions ct
            LEFT JOIN users u ON ct.admin_id = u.id
            WHERE ct.created_at BETWEEN ? AND ?
            GROUP BY ct.type, u.name
            ORDER BY ct.type, total_amount DESC
        ", [
            $dateFilter['start'], $dateFilter['end']
        ]);
        
        return $manualCoinStats;
    }
    
    private function getVisitorStats($dateFilter)
    {
        // Get visitor statistics
        $visitorStats = DB::select("
            SELECT 
                COALESCE(SUM(total_visits), 0) as total_visits,
                COALESCE(SUM(unique_visitors), 0) as unique_visitors,
                COALESCE(SUM(page_views), 0) as page_views,
                COALESCE(SUM(new_users), 0) as new_users,
                COALESCE(SUM(returning_users), 0) as returning_users,
                COALESCE(AVG(total_visits), 0) as avg_daily_visits,
                COALESCE(AVG(unique_visitors), 0) as avg_daily_unique_visitors
            FROM visitor_stats 
            WHERE date BETWEEN ? AND ?
        ", [
            $dateFilter['start']->format('Y-m-d'), 
            $dateFilter['end']->format('Y-m-d')
        ])[0];
        
        return (array) $visitorStats;
    }
    
    private function getOnlineStats()
    {
        // Get online users statistics
        $onlineUsers = OnlineUser::online()->count();
        $onlineGuests = OnlineUser::online()->guests()->count();
        $onlineRegisteredUsers = OnlineUser::online()->users()->count();
        
        // Get top pages being viewed
        $topPages = OnlineUser::online()
            ->select('current_page', DB::raw('COUNT(*) as view_count'))
            ->whereNotNull('current_page')
            ->groupBy('current_page')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();
        
        return [
            'total_online' => $onlineUsers,
            'online_guests' => $onlineGuests,
            'online_users' => $onlineRegisteredUsers,
            'top_pages' => $topPages
        ];
    }
}

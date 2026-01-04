<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default to current day
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $day = $request->get('day', date('d'));
        
        // Build date filter
        $dateFilter = $this->buildDateFilter($year, $month, $day);
        
        // Get basic stats
        $basicStats = $this->getBasicStats($dateFilter);
        
        // Get story view statistics
        $storyViews = $this->getStoryViewStats($dateFilter);
        
        // Đã đóng - Nhiệm Vụ Hàng Ngày
        // Get daily task statistics
        // $dailyTaskStats = $this->getDailyTaskStats($dateFilter);
        
        // Check if user is admin_main to show revenue-related data
        $isAdminMain = Auth::user()->role === 'admin_main';
        
        $data = compact(
            'basicStats',
            'storyViews',
            // 'dailyTaskStats', // Đã đóng
            'year',
            'month',
            'day',
            'isAdminMain'
        );
        
        // Only include revenue data for admin_main
        if ($isAdminMain) {
            $revenueStats = $this->getRevenueStats($dateFilter);
            // $coinStats = $this->getCoinStats($dateFilter); // Đã đóng
            // Đã đóng - Thống Kê Nạp Cám
            // $depositStats = $this->getDepositStats($dateFilter);
            // Đã đóng - Giao Dịch Cám Thủ Công
            // $manualCoinStats = $this->getManualCoinStats($dateFilter);
            
            $data = array_merge($data, compact(
                'revenueStats',
                // 'coinStats', // Đã đóng
                // 'depositStats', // Đã đóng
                // 'manualCoinStats' // Đã đóng
            ));
        }
        
        return view('admin.pages.dashboard', $data);
    }
    
    public function getStatsData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $day = $request->get('day', date('d'));
        
        $dateFilter = $this->buildDateFilter($year, $month, $day);
        
        // Check if user is admin_main to show revenue-related data
        $isAdminMain = Auth::user()->role === 'admin_main';
        
        $data = [
            'basicStats' => $this->getBasicStats($dateFilter),
            'storyViews' => $this->getStoryViewStats($dateFilter),
            // Đã đóng - Nhiệm Vụ Hàng Ngày
            // 'dailyTaskStats' => $this->getDailyTaskStats($dateFilter),
        ];
        
        // Only include revenue data for admin_main
        if ($isAdminMain) {
            $data = array_merge($data, [
                'revenueStats' => $this->getRevenueStats($dateFilter),
                // 'coinStats' => $this->getCoinStats($dateFilter), // Đã đóng
                // Đã đóng - Thống Kê Nạp Cám
                // 'depositStats' => $this->getDepositStats($dateFilter),
                // Đã đóng - Giao Dịch Cám Thủ Công
                // 'manualCoinStats' => $this->getManualCoinStats($dateFilter),
            ]);
        }
        
        return response()->json($data);
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
        // Get revenue by story - combine story purchases and chapter purchases for each story
        $revenueStats = DB::select("
            SELECT 
                s.id,
                s.title,
                s.author_name,
                COALESCE(story_revenue.total_revenue, 0) + COALESCE(chapter_revenue.total_revenue, 0) as total_revenue,
                COALESCE(story_revenue.author_revenue, 0) + COALESCE(chapter_revenue.author_revenue, 0) as author_revenue,
                COALESCE(story_revenue.purchase_count, 0) + COALESCE(chapter_revenue.purchase_count, 0) as purchase_count
            FROM stories s
            LEFT JOIN (
                SELECT 
                    sp.story_id,
                    COALESCE(SUM(sp.amount_paid), 0) as total_revenue,
                    COALESCE(SUM(sp.amount_received), 0) as author_revenue,
                    COUNT(sp.id) as purchase_count
                FROM story_purchases sp
                WHERE sp.created_at BETWEEN ? AND ?
                GROUP BY sp.story_id
            ) story_revenue ON s.id = story_revenue.story_id
            LEFT JOIN (
                SELECT 
                    s2.id as story_id,
                    COALESCE(SUM(cp.amount_paid), 0) as total_revenue,
                    COALESCE(SUM(cp.amount_received), 0) as author_revenue,
                    COUNT(cp.id) as purchase_count
                FROM stories s2
                INNER JOIN chapters c ON s2.id = c.story_id
                INNER JOIN chapter_purchases cp ON c.id = cp.chapter_id
                WHERE cp.created_at BETWEEN ? AND ?
                GROUP BY s2.id
            ) chapter_revenue ON s.id = chapter_revenue.story_id
            WHERE s.status = 'published'
                AND (COALESCE(story_revenue.total_revenue, 0) + COALESCE(chapter_revenue.total_revenue, 0) > 0)
            ORDER BY total_revenue DESC
        ", [
            $dateFilter['start']->format('Y-m-d H:i:s'), $dateFilter['end']->format('Y-m-d H:i:s'),
            $dateFilter['start']->format('Y-m-d H:i:s'), $dateFilter['end']->format('Y-m-d H:i:s')
        ]);
        
        return $revenueStats;
    }
    
    // Đã đóng - Bỏ mục Tổng Cám người dùng, Cám đã nạp và Cám nhiệm vụ
    // private function getCoinStats($dateFilter)
    // {
    //     // Get total coin statistics
    //     $coinStats = DB::select("
    //         SELECT 
    //             (SELECT COALESCE(SUM(coins), 0) FROM users WHERE active = 'active') as total_user_coins,
    //             (
    //                 (SELECT COALESCE(SUM(total_coins), 0) FROM deposits WHERE status = 'approved' AND created_at BETWEEN ? AND ?) +
    //                 (SELECT COALESCE(SUM(total_coins), 0) FROM paypal_deposits WHERE status = 'approved' AND created_at BETWEEN ? AND ?) +
    //                 (SELECT COALESCE(SUM(total_coins), 0) FROM card_deposits WHERE status = 'success' AND created_at BETWEEN ? AND ?) +
    //                 (SELECT COALESCE(SUM(total_coins), 0) FROM bank_auto_deposits WHERE status = 'success' AND created_at BETWEEN ? AND ?)
    //             ) as total_deposited,
    //             (SELECT COALESCE(SUM(udt.coin_reward * udt.completed_count), 0) FROM user_daily_tasks udt WHERE udt.created_at BETWEEN ? AND ?) as total_daily_task_coins,
    //             (SELECT COALESCE(SUM(amount), 0) FROM coin_transactions WHERE type = 'add' AND created_at BETWEEN ? AND ?) as total_manual_added,
    //             (SELECT COALESCE(SUM(amount), 0) FROM coin_transactions WHERE type = 'subtract' AND created_at BETWEEN ? AND ?) as total_manual_subtracted
    //     ", [
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end']
    //     ])[0];
    //     
    //     return (array) $coinStats;
    // }
    
    // Đã đóng - Thống Kê Nạp Cám
    // private function getDepositStats($dateFilter)
    // {
    //     // Get deposit statistics by type
    //     $depositStats = DB::select("
    //         SELECT 
    //             'bank' as type,
    //             COUNT(*) as count,
    //             COALESCE(SUM(total_coins), 0) as total_amount,
    //             COALESCE(AVG(total_coins), 0) as avg_amount
    //         FROM deposits 
    //         WHERE status = 'approved' AND created_at BETWEEN ? AND ?
    //         
    //         UNION ALL
    //         
    //         SELECT 
    //             'paypal' as type,
    //             COUNT(*) as count,
    //             COALESCE(SUM(total_coins), 0) as total_amount,
    //             COALESCE(AVG(total_coins), 0) as avg_amount
    //         FROM paypal_deposits 
    //         WHERE status = 'approved' AND created_at BETWEEN ? AND ?
    //         
    //         UNION ALL
    //         
    //         SELECT 
    //             'card' as type,
    //             COUNT(*) as count,
    //             COALESCE(SUM(total_coins), 0) as total_amount,
    //             COALESCE(AVG(total_coins), 0) as avg_amount
    //         FROM card_deposits 
    //         WHERE status = 'success' AND created_at BETWEEN ? AND ?
    //         
    //         UNION ALL
    //         
    //         SELECT 
    //             'bank_auto' as type,
    //             COUNT(*) as count,
    //             COALESCE(SUM(total_coins), 0) as total_amount,
    //             COALESCE(AVG(total_coins), 0) as avg_amount
    //         FROM bank_auto_deposits 
    //         WHERE status = 'success' AND created_at BETWEEN ? AND ?
    //     ", [
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end'],
    //         $dateFilter['start'], $dateFilter['end']
    //     ]);
    //     
    //     return $depositStats;
    // }
    
    // Đã đóng - Nhiệm Vụ Hàng Ngày
    // private function getDailyTaskStats($dateFilter)
    // {
    //     // Get daily task statistics
    //     $dailyTaskStats = DB::select("
    //         SELECT 
    //             dt.name,
    //             dt.type,
    //             COUNT(udt.id) as completion_count,
    //             COALESCE(AVG(udt.coin_reward), 0) as avg_coins_per_task,
    //             COALESCE(SUM(udt.coin_reward * udt.completed_count), 0) as total_coins_distributed
    //         FROM daily_tasks dt
    //         LEFT JOIN user_daily_tasks udt ON dt.id = udt.daily_task_id 
    //             AND udt.created_at BETWEEN ? AND ?
    //         WHERE dt.active = 1
    //         GROUP BY dt.id, dt.name, dt.type
    //         ORDER BY completion_count DESC
    //     ", [
    //         $dateFilter['start'], $dateFilter['end']
    //     ]);
    //     
    //     return $dailyTaskStats;
    // }
    
    // Đã đóng - Giao Dịch Cám Thủ Công
    // private function getManualCoinStats($dateFilter)
    // {
    //     // Get manual coin transaction statistics
    //     $manualCoinStats = DB::select("
    //         SELECT 
    //             ct.type,
    //             COUNT(*) as transaction_count,
    //             COALESCE(SUM(ct.amount), 0) as total_amount,
    //             COALESCE(AVG(ct.amount), 0) as avg_amount,
    //             u.name as admin_name
    //         FROM coin_transactions ct
    //         LEFT JOIN users u ON ct.admin_id = u.id
    //         WHERE ct.created_at BETWEEN ? AND ?
    //         GROUP BY ct.type, u.name
    //         ORDER BY ct.type, total_amount DESC
    //     ", [
    //         $dateFilter['start'], $dateFilter['end']
    //     ]);
    //     
    //     return $manualCoinStats;
    // }
}
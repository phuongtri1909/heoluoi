<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\UserDailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTaskController extends Controller
{
    /**
     * Hiển thị danh sách nhiệm vụ hàng ngày
     */
    public function index()
    {
        $tasks = DailyTask::orderBy('order')->orderBy('id')->paginate(10);
        
        return view('admin.pages.daily-tasks.index', compact('tasks'));
    }

  

    /**
     * Hiển thị chi tiết nhiệm vụ
     */
    public function show(DailyTask $dailyTask)
    {
       
        $stats = [
            'total_completions' => UserDailyTask::where('daily_task_id', $dailyTask->id)->sum('completed_count'),
            'unique_users' => UserDailyTask::where('daily_task_id', $dailyTask->id)->distinct('user_id')->count(),
            'today_completions' => UserDailyTask::where('daily_task_id', $dailyTask->id)
                ->where('task_date', now()->format('Y-m-d'))
                ->sum('completed_count'),
            'this_week_completions' => UserDailyTask::where('daily_task_id', $dailyTask->id)
                ->whereBetween('task_date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')])
                ->sum('completed_count'),
        ];

       
        $recentCompletions = UserDailyTask::where('daily_task_id', $dailyTask->id)
            ->with('user')
            ->orderBy('last_completed_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.pages.daily-tasks.show', compact('dailyTask', 'stats', 'recentCompletions'));
    }

    /**
     * Hiển thị form chỉnh sửa nhiệm vụ
     */
    public function edit(DailyTask $dailyTask)
    {
        return view('admin.pages.daily-tasks.edit', compact('dailyTask'));
    }

    /**
     * Cập nhật nhiệm vụ
     */
    public function update(Request $request, DailyTask $dailyTask)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:login,comment,bookmark,share',
            'description' => 'nullable|string',
            'coin_reward' => 'required|integer|min:0',
            'max_per_day' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0'
        ]);

        $dailyTask->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'coin_reward' => $request->coin_reward,
            'max_per_day' => $request->max_per_day,
            'active' => $request->has('active'),
            'order' => $request->order ?? 0
        ]);

        return redirect()->route('admin.daily-tasks.index')
            ->with('success', 'Nhiệm vụ đã được cập nhật thành công!');
    }


    /**
     * Toggle trạng thái active của nhiệm vụ
     */
    public function toggleActive(DailyTask $dailyTask)
    {
        $dailyTask->update(['active' => !$dailyTask->active]);

        $status = $dailyTask->active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.daily-tasks.index')
            ->with('success', "Nhiệm vụ đã được {$status} thành công!");
    }

    /**
     * Hiển thị danh sách tiến độ của user
     */
    public function userProgress()
    {
        $userProgress = UserDailyTask::with(['user', 'dailyTask'])
            ->orderBy('task_date', 'desc')
            ->orderBy('last_completed_at', 'desc')
            ->paginate(20);

        return view('admin.pages.daily-tasks.user-progress', compact('userProgress'));
    }

    /**
     * Hiển thị thống kê tổng quan
     */
    public function statistics()
    {
        $stats = [
            'total_tasks' => DailyTask::count(),
            'active_tasks' => DailyTask::where('active', true)->count(),
            'total_completions' => UserDailyTask::sum('completed_count'),
            'unique_users' => UserDailyTask::distinct('user_id')->count(),
            'today_completions' => UserDailyTask::where('task_date', now()->format('Y-m-d'))->sum('completed_count'),
            'this_week_completions' => UserDailyTask::whereBetween('task_date', [
                now()->startOfWeek()->format('Y-m-d'), 
                now()->endOfWeek()->format('Y-m-d')
            ])->sum('completed_count'),
        ];

        $taskTypeStats = DailyTask::with(['userDailyTasks'])->get()->map(function($task) {
            $totalCompletions = $task->userDailyTasks->sum('completed_count');
            return (object) [
                'id' => $task->id,
                'name' => $task->name,
                'type' => $task->type,
                'total_completions' => $totalCompletions
            ];
        });

        $dailyStats = UserDailyTask::select(
            'task_date',
            DB::raw('sum(completed_count) as total_completions'),
            DB::raw('count(distinct user_id) as unique_users')
        )
        ->where('task_date', '>=', now()->subDays(7)->format('Y-m-d'))
        ->groupBy('task_date')
        ->orderBy('task_date')
        ->get();

        return view('admin.pages.daily-tasks.statistics', compact('stats', 'taskTypeStats', 'dailyStats'));
    }
}
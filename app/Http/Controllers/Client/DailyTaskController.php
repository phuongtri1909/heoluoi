<?php

namespace App\Http\Controllers\Client;

use App\Models\DailyTask;
use App\Models\UserDailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class DailyTaskController extends Controller
{

    /**
     * Hiển thị trang nhiệm vụ hàng ngày
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $tasks = DailyTask::active()->ordered()->get();

        $userTasks = \App\Models\UserDailyTask::where('user_id', $user->id)
            ->where('task_date', $today)
            ->get()
            ->keyBy('daily_task_id');

        $tasks = $tasks->map(function ($task) use ($userTasks) {
            $userTask = $userTasks->get($task->id);
            $completedCount = $userTask ? $userTask->completed_count : 0;
            $isCompleted = $completedCount >= $task->max_per_day;
            
            return [
                'id' => $task->id,
                'name' => $task->name,
                'type' => $task->type,
                'description' => $task->description,
                'coin_reward' => $task->coin_reward,
                'max_per_day' => $task->max_per_day,
                'completed_count' => $completedCount,
                'is_completed' => $isCompleted,
                'progress_percentage' => ($completedCount / $task->max_per_day) * 100,
            ];
        });

        $stats = UserDailyTask::getUserTaskStats($user->id);

        $history = UserDailyTask::getUserTaskHistory($user->id, 10);

        return view('pages.information.daily_tasks', compact('tasks', 'stats', 'history'));
    }

    /**
     * Complete login task
     */
    public function completeLogin(Request $request)
    {
        $result = UserDailyTask::completeTask(
            Auth::id(),
            DailyTask::TYPE_LOGIN,
            ['login_time' => now()->toISOString()],
            $request
        );

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Complete comment task
     */
    public function completeComment(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'comment_id' => 'required|exists:comments,id',
        ]);

        $result = UserDailyTask::completeTask(
            Auth::id(),
            DailyTask::TYPE_COMMENT,
            [
                'story_id' => $request->story_id,
                'comment_id' => $request->comment_id,
                'comment_time' => now()->toISOString(),
            ],
            $request
        );

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return response()->json($result);
    }

    /**
     * Complete bookmark task
     */
    public function completeBookmark(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
        ]);

        $result = UserDailyTask::completeTask(
            Auth::id(),
            DailyTask::TYPE_BOOKMARK,
            [
                'story_id' => $request->story_id,
                'bookmark_time' => now()->toISOString(),
            ],
            $request
        );

        return response()->json($result);
    }

    /**
     * Complete share task
     */
    public function completeShare(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'platform' => 'required|string|in:facebook,twitter,telegram,zalo,copy',
        ]);

        $result = UserDailyTask::completeTask(
            Auth::id(),
            DailyTask::TYPE_SHARE,
            [
                'story_id' => $request->story_id,
                'platform' => $request->platform,
                'share_time' => now()->toISOString(),
            ],
            $request
        );

        return response()->json($result);
    }

    /**
     * Get today task status
     */
    public function getTodayStatus(Request $request)
    {
        $user = Auth::user();
        $tasks = DailyTask::active()->ordered()->get();

        $taskStatus = $tasks->map(function ($task) use ($user) {
            return [
                'type' => $task->type,
                'completed_count' => $task->getCompletedCountToday($user->id),
                'max_per_day' => $task->max_per_day,
                'is_completed' => $task->isCompletedByUserToday($user->id),
                'coin_reward' => $task->coin_reward,
            ];
        });

        return response()->json([
            'success' => true,
            'tasks' => $taskStatus,
            'user_coins' => $user->coins,
        ]);
    }

    /**
     * Get task history (pagination)
     */
    public function getHistory(Request $request)
    {
        $history = UserDailyTask::getUserTaskHistory(Auth::id(), 20);
        
        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}
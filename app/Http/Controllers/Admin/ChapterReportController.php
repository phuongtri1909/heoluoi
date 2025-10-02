<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChapterReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChapterReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ChapterReport::with(['user', 'chapter', 'story'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'like', '%' . $search . '%')
                           ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('story', function($story) use ($search) {
                      $story->where('title', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('chapter', function($chapter) use ($search) {
                      $chapter->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        $reports = $query->paginate(20);
        
        return view('admin.pages.chapter-reports.index', compact('reports'));
    }

    public function show(ChapterReport $report)
    {
        $report->load(['user', 'chapter', 'story']);
        
        return view('admin.pages.chapter-reports.show', compact('report'));
    }

    public function updateStatus(Request $request, ChapterReport $report)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,resolved,rejected',
            'admin_response' => 'nullable|string|max:1000'
        ],[
            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
            'admin_response.string' => 'Phản hồi phải là chuỗi',
            'admin_response.max' => 'Phản hồi không được vượt quá 1000 ký tự',
        ]);

        $report->update([
            'status' => $request->get('status'),
            'admin_response' => $request->get('admin_response')
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'selected_reports' => 'required|array',
            'selected_reports.*' => 'exists:chapter_reports,id',
            'action' => 'required|in:mark_processing,mark_resolved,mark_rejected,delete'
        ],[
            'selected_reports.required' => 'Báo cáo không được để trống',
            'selected_reports.array' => 'Báo cáo phải là một mảng',
            'selected_reports.exists' => 'Báo cáo không tồn tại',
            'action.required' => 'Hành động không được để trống',
            'action.in' => 'Hành động không hợp lệ',
        ]);

        $reportIds = $request->get('selected_reports');
        $action = $request->get('action');

        switch ($action) {
            case 'mark_processing':
                ChapterReport::whereIn('id', $reportIds)->update(['status' => 'processing']);
                break;
            case 'mark_resolved':
                ChapterReport::whereIn('id', $reportIds)->update(['status' => 'resolved']);
                break;
            case 'mark_rejected':
                ChapterReport::whereIn('id', $reportIds)->update(['status' => 'rejected']);
                break;
            case 'delete':
                ChapterReport::whereIn('id', $reportIds)->delete();
                break;
        }

        $actionText = match($action) {
            'mark_processing' => 'Đánh dấu đang xử lý',
            'mark_resolved' => 'Đánh dấu đã xử lý', 
            'mark_rejected' => 'Đánh dấu từ chối',
            'delete' => 'Xóa',
            default => 'Thực hiện'
        };

        return back()->with('success', $actionText . ' ' . count($reportIds) . ' báo cáo thành công!');
    }

    public function statsApi(Request $request): JsonResponse
    {
        $stats = [
            'total' => ChapterReport::count(),
            'pending' => ChapterReport::where('status', 'pending')->count(),
            'processing' => ChapterReport::where('status', 'processing')->count(),
            'resolved' => ChapterReport::where('status', 'resolved')->count(),
            'rejected' => ChapterReport::where('status', 'rejected')->count()
        ];

        return response()->json($stats);
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChapterReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'description' => 'required|string|min:10|max:1000'
        ],[
            'chapter_id.required' => 'Chương không được để trống',
            'chapter_id.exists' => 'Chương không tồn tại',
            'description.required' => 'Mô tả không được để trống',
            'description.string' => 'Mô tả phải là chuỗi',
            'description.min' => 'Mô tả không được ít hơn 10 ký tự',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
        ]);

        $chapterId = $request->get('chapter_id');
        
        $existingReport = ChapterReport::where('user_id', Auth::id())
            ->where('chapter_id', $chapterId)
            ->where('status', 'pending')
            ->exists();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã báo cáo chương này rồi. Vui lòng chờ admin xử lý.'
            ], 400);
        }

        $chapter = \App\Models\Chapter::findOrFail($chapterId);

        ChapterReport::create([
            'user_id' => Auth::id(),
            'chapter_id' => $chapterId,
            'story_id' => $chapter->story_id,
            'description' => $request->get('description'),
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi báo cáo thành công! Cảm ơn bạn đã đóng góp ý kiến.'
        ]);
    }

    public function userReports(Request $request)
    {
        $reports = ChapterReport::with(['chapter', 'story'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pages.information.user.chapter_reports', compact('reports'));
    }
}
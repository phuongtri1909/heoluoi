<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Models\Story;
use App\Models\Rating;
use App\Models\Status;
use App\Models\Chapter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ChapterController extends Controller
{
    public function index(Request $request, Story $story)
    {
        $user = Auth::user();
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể xem chapter của truyện mình.');
        }

        $search = $request->search;
        $status = $request->status;
        $query = $story->chapters();

        $totalChapters = $query->count();
        $publishedChapters = $story->chapters()->where('status', 'published')->count();
        $draftChapters = $story->chapters()->where('status', 'draft')->count();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $searchNumber = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $searchNumber) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('number', 'like', "%$search%");

                if (is_numeric($searchNumber)) {
                    $q->orWhere('number', '=', (int)$searchNumber);
                }
            });
        }

        $chapters = $query->orderBy('number', 'DESC')->paginate(15);

        foreach ($chapters as $chapter) {
            $content = strip_tags($chapter->content);
            $chapter->content = mb_substr($content, 0, 97, 'UTF-8') . '...';
        }

        return view('admin.pages.chapters.index', compact(
            'story',
            'chapters',
            'totalChapters',
            'publishedChapters',
            'draftChapters',
        ));
    }

    public function create(Story $story)
    {
        $user = Auth::user();
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể tạo chapter cho truyện của mình.');
        }

        $latestChapterNumber = $story->chapters()->max('number') ?? 0;
        $nextChapterNumber = $latestChapterNumber + 1;

        return view('admin.pages.chapters.create', compact('story', 'nextChapterNumber'));
    }

    public function store(Request $request, Story $story)
    {
        $user = Auth::user();
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể tạo chapter cho truyện của mình.');
        }

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'number' => [
                'required',
                function ($attribute, $value, $fail) use ($story) {
                    if ($story->chapters()->where('number', $value)->exists()) {
                        $fail('Chương ' . $value . ' đã tồn tại trong truyện này');
                    }
                },
                'integer',
            ],
            'status' => 'required|in:draft,published',
            'price' => 'nullable|integer|min:0',
        ], [
            'title.required' => 'Tên chương không được để trống',
            'content.required' => 'Nội dung chương không được để trống',
            'number.required' => 'Số chương không được để trống',
            'number.integer' => 'Số chương phải là số nguyên',
            'status.required' => 'Trạng thái chương không được để trống',
            'status.in' => 'Trạng thái chương không hợp lệ',
            'price.integer' => 'Giá phải là số nguyên',
            'price.min' => 'Giá không được âm',
        ]);

        try {
            $isFree = $request->has('is_free');

            $price = $isFree ? 0 : $request->price;

            $chapter = $story->chapters()->create([
                'title' => $request->title,
                'content' => $request->content,
                'number' => $request->number,
                'status' => $request->status,
                'is_free' => $isFree,
                'price' => $price,
                'slug' => 'temp-slug-' . time(),
                'published_at' => now(),
            ]);

            $chapter->update([
                'slug' => $chapter->id . '-chuong' . $request->number
            ]);

            return redirect()->route('admin.stories.chapters.index', $story)
                ->with('success', 'Tạo chương ' . $request->number . ' thành công');
        } catch (\Exception $e) {
            Log::error('Chapter creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại')
                ->withInput();
        }
    }

    public function edit(Story $story, Chapter $chapter)
    {
        $user = Auth::user();
        // admin_sub chỉ có thể sửa chapter của truyện mình
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể sửa chapter của truyện mình.');
        }

        return view('admin.pages.chapters.edit', compact('story', 'chapter'));
    }

    public function update(Request $request, Story $story, Chapter $chapter)
    {
        $user = Auth::user();
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể sửa chapter của truyện mình.');
        }

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'number' => [
                'required',
                function ($attribute, $value, $fail) use ($story, $chapter) {
                    if ($story->chapters()
                        ->where('number', $value)
                        ->where('id', '!=', $chapter->id)
                        ->exists()
                    ) {
                        $fail('Chương số ' . $value . ' đã tồn tại trong truyện này');
                    }
                },
                'integer',
            ],
            'status' => 'required|in:draft,published',
            'price' => 'nullable|integer|min:0',
            'scheduled_publish_at' => 'nullable|date|after:now',
        ],[
            'title.required' => 'Tên chương không được để trống',
            'content.required' => 'Nội dung chương không được để trống',
            'number.required' => 'Số chương không được để trống',
            'number.integer' => 'Số chương phải là số nguyên',
            'status.required' => 'Trạng thái chương không được để trống',
            'status.in' => 'Trạng thái chương không hợp lệ',
            'price.integer' => 'Giá phải là số nguyên',
            'price.min' => 'Giá không được âm',
            'scheduled_publish_at.date' => 'Thời gian hẹn đăng không hợp lệ',
            'scheduled_publish_at.after' => 'Thời gian hẹn đăng phải sau thời điểm hiện tại',
        ]);

        try {
            $isFree = $request->has('is_free');
            $price = $isFree ? 0 : $request->price;

            // Handle scheduled_publish_at based on status
            $scheduledPublishAt = null;
            $publishedAt = null;

            if ($request->status === 'draft' && $request->scheduled_publish_at) {
                $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_publish_at, 'Asia/Ho_Chi_Minh');
                $now = now();
                
                if ($scheduledTime->gt($now)) {
                    $scheduledPublishAt = $scheduledTime;
                    $publishedAt = null;
                } else {
                    // If scheduled time is in the past, publish immediately
                    $publishedAt = $scheduledTime;
                    $scheduledPublishAt = null;
                }
            } elseif ($request->status === 'published') {
                $publishedAt = now();
                $scheduledPublishAt = null;
            }

            $updateData = [
                'title' => $request->title,
                'content' => $request->content,
                'number' => $request->number,
                'status' => $request->status,
                'is_free' => $isFree,
                'price' => $price,
                'slug' => $chapter->id . '-chuong' . $request->number,
                'published_at' => $publishedAt,
                'scheduled_publish_at' => $scheduledPublishAt,
            ];

            $chapter->update($updateData);

            return redirect()->route('admin.stories.chapters.index', $story)
                ->with('success', 'Cập nhật chương ' . $request->number . ' thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại')
                ->withInput();
        }
    }

    public function show(Story $story, Chapter $chapter)
    {
        $user = Auth::user();
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể xem chapter của truyện mình.');
        }

        return view('admin.pages.chapters.show', compact('story', 'chapter'));
    }

    public function destroy(Story $story, Chapter $chapter)
    {
        $user = Auth::user();
        if ($user->role === 'admin_sub' && $story->user_id != $user->id) {
            abort(403, 'Bạn chỉ có thể xóa chapter của truyện mình.');
        }

        try {
            $canDelete = $this->canDeleteChapter($chapter);
            
            if (!$canDelete['can_delete']) {
                return redirect()->back()
                    ->with('error', $canDelete['message']);
            }
            
            $chapter->delete();
            
            $referer = request()->header('referer');
            if ($referer && str_contains($referer, route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $chapter]))) {
                return redirect()->route('admin.stories.chapters.index', $story)
                    ->with('success', 'Xóa chương thành công');
            }
            
            if ($referer) {
                return redirect()->back()
                    ->with('success', 'Xóa chương thành công');
            } else {
                return redirect()->route('admin.stories.chapters.index', $story)
                    ->with('success', 'Xóa chương thành công');
            }
        } catch (\Exception $e) {
            $referer = request()->header('referer');
            if ($referer && str_contains($referer, route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $chapter]))) {
                return redirect()->route('admin.stories.chapters.index', $story)
                    ->with('error', 'Có lỗi xảy ra, vui lòng thử lại');
            }
            
            if ($referer) {
                return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra, vui lòng thử lại');
            } else {
                return redirect()->route('admin.stories.chapters.index', $story)
                    ->with('error', 'Có lỗi xảy ra, vui lòng thử lại');
            }
        }
    }

    /**
     * Show bulk create form
     */
    public function bulkCreate(Story $story)
    {
        return view('admin.pages.chapters.bulk-create', compact('story'));
    }

    /**
     * Check existing chapters
     */
    public function checkExisting(Request $request, Story $story)
    {
        $request->validate([
            'chapter_numbers' => 'required|array',
            'chapter_numbers.*' => 'integer|min:1'
        ]);

        $existingNumbers = $story->chapters()
            ->whereIn('number', $request->chapter_numbers)
            ->pluck('number')
            ->toArray();

        return response()->json([
            'existing' => $existingNumbers,
            'available' => array_diff($request->chapter_numbers, $existingNumbers)
        ]);
    }

    /**
     * Store multiple chapters
     */
    public function bulkStore(Request $request, Story $story)
    {
        $validated = $request->validate([
            'chapters' => 'required|string',
        ]);

        $chaptersData = json_decode($validated['chapters'], true);
        
        if (!is_array($chaptersData)) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu chương không hợp lệ'
            ], 400);
        }

        // Validate each chapter data
        foreach ($chaptersData as $index => $chapterData) {
            if (!isset($chapterData['number']) || !is_numeric($chapterData['number']) || $chapterData['number'] < 1) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " có số chương không hợp lệ"
                ], 400);
            }
            if (!isset($chapterData['title']) || empty(trim($chapterData['title']))) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " thiếu tên chương"
                ], 400);
            }
            if (!isset($chapterData['content']) || empty(trim($chapterData['content']))) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " thiếu nội dung"
                ], 400);
            }
            if (!isset($chapterData['price']) || !is_numeric($chapterData['price']) || $chapterData['price'] < 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " có giá không hợp lệ"
                ], 400);
            }
            // Only validate published_at if not publishing now
            if (!isset($chapterData['publish_now']) || !$chapterData['publish_now']) {
                if (!isset($chapterData['published_at']) || empty($chapterData['published_at'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Chương thứ " . ($index + 1) . " thiếu ngày công bố"
                    ], 400);
                }
            }
        }

        // Get existing chapter numbers
        $existingNumbers = $story->chapters()
            ->whereIn('number', collect($chaptersData)->pluck('number'))
            ->pluck('number')
            ->toArray();

        $createdCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($chaptersData as $chapterData) {
                // Skip if chapter already exists
                if (in_array($chapterData['number'], $existingNumbers)) {
                    continue;
                }

                $slug = Str::slug($story->slug . '-' . $chapterData['number'] . '-' . $chapterData['title']);
                
                // Check for slug uniqueness
                $originalSlug = $slug;
                $counter = 1;
                while (Chapter::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                // Determine status and published_at based on publish_now flag or scheduled time
                $publishNow = isset($chapterData['publish_now']) ? $chapterData['publish_now'] : false;
                
                if ($publishNow) {
                    // Publish immediately using server time
                    $status = 'published';
                    $publishedAt = now();
                    $scheduledPublishAt = null;
                } else {
                    // Use scheduled time
                    if (isset($chapterData['published_at']) && !empty($chapterData['published_at'])) {
                        // Parse the datetime string and assume it's in Vietnam timezone
                        $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $chapterData['published_at'], 'Asia/Ho_Chi_Minh');
                        $now = now();
                        
                        $status = 'published';
                        $publishedAt = $scheduledTime;
                        $scheduledPublishAt = null;
                        
                        if ($scheduledTime->gt($now)) {
                            $status = 'draft';
                            $publishedAt = null;
                            $scheduledPublishAt = $scheduledTime;
                        }
                    } else {
                        // Fallback to current time if no published_at provided
                        $status = 'published';
                        $publishedAt = now();
                        $scheduledPublishAt = null;
                    }
                }

                $chapter = Chapter::create([
                    'story_id' => $story->id,
                    'number' => $chapterData['number'],
                    'title' => $chapterData['title'],
                    'slug' => 'temp-slug-' . time(),
                    'content' => $chapterData['content'],
                    'price' => $chapterData['price'],
                    'is_free' => $chapterData['price'] == 0,
                    'status' => $status,
                    'published_at' => $publishedAt,
                    'scheduled_publish_at' => $scheduledPublishAt,
                    'views' => 0,
                ]);

                // Update slug after creation
                $chapter->update([
                    'slug' => $chapter->id . '-chuong' . $chapterData['number']
                ]);

                $createdCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã tạo thành công {$createdCount} chương",
                'created_count' => $createdCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo chương: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a chapter can be deleted
     */
    private function canDeleteChapter(Chapter $chapter)
    {
        if ($chapter->is_free) {
            return ['can_delete' => true, 'message' => ''];
        }

        $hasComboPurchases = $chapter->story->purchases()->exists();
        
        $hasChapterPurchases = $chapter->purchases()->exists();

        if ($hasComboPurchases) {
            return [
                'can_delete' => false, 
                'message' => 'Không thể xóa chương này vì đã có người mua combo truyện'
            ];
        }

        if ($hasChapterPurchases) {
            return [
                'can_delete' => false, 
                'message' => 'Không thể xóa chương này vì đã có người mua chương riêng lẻ'
            ];
        }

        return ['can_delete' => true, 'message' => ''];
    }

    /**
     * Bulk delete chapters
     */
    public function bulkDestroy(Request $request, Story $story)
    {
        $request->validate([
            'chapter_ids' => 'required|array',
            'chapter_ids.*' => 'exists:chapters,id'
        ]);

        $chapterIds = $request->chapter_ids;
        $chapters = $story->chapters()->whereIn('id', $chapterIds)->get();
        
        $deletedCount = 0;
        $errors = [];
        $cannotDelete = [];

        DB::beginTransaction();
        try {
            foreach ($chapters as $chapter) {
                $canDelete = $this->canDeleteChapter($chapter);
                
                if ($canDelete['can_delete']) {
                    $chapter->delete();
                    $deletedCount++;
                } else {
                    $cannotDelete[] = [
                        'chapter_number' => $chapter->number,
                        'title' => $chapter->title,
                        'reason' => $canDelete['message']
                    ];
                }
            }

            DB::commit();

            $message = "Đã xóa thành công {$deletedCount} chương";
            $details = [];
            
            if (count($cannotDelete) > 0) {
                $message .= ". Không thể xóa " . count($cannotDelete) . " chương";
                foreach ($cannotDelete as $item) {
                    $details[] = "Chương {$item['chapter_number']} ({$item['title']}) - {$item['reason']}";
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'cannot_delete_count' => count($cannotDelete),
                'details' => $details
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa chương: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check which chapters can be deleted (for bulk operations)
     */
    public function checkDeletable(Request $request, Story $story)
    {
        $request->validate([
            'chapter_ids' => 'required|array',
            'chapter_ids.*' => 'exists:chapters,id'
        ]);

        $chapterIds = $request->chapter_ids;
        $chapters = $story->chapters()->whereIn('id', $chapterIds)->get();
        
        $deletable = [];
        $notDeletable = [];

        foreach ($chapters as $chapter) {
            $canDelete = $this->canDeleteChapter($chapter);
            
            if ($canDelete['can_delete']) {
                $deletable[] = [
                    'id' => $chapter->id,
                    'number' => $chapter->number,
                    'title' => $chapter->title
                ];
            } else {
                $notDeletable[] = [
                    'id' => $chapter->id,
                    'number' => $chapter->number,
                    'title' => $chapter->title,
                    'reason' => $canDelete['message']
                ];
            }
        }

        return response()->json([
            'deletable' => $deletable,
            'not_deletable' => $notDeletable,
            'total_deletable' => count($deletable),
            'total_not_deletable' => count($notDeletable)
        ]);
    }
}

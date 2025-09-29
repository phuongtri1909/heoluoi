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
use App\Http\Controllers\Controller;

class ChapterController extends Controller
{
    public function index(Request $request, Story $story)
    {

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
        $latestChapterNumber = $story->chapters()->max('number') ?? 0;
        $nextChapterNumber = $latestChapterNumber + 1;

        return view('admin.pages.chapters.create', compact('story', 'nextChapterNumber'));
    }

    public function store(Request $request, Story $story)
    {
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
        return view('admin.pages.chapters.edit', compact('story', 'chapter'));
    }

    public function update(Request $request, Story $story, Chapter $chapter)
    {
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
        ],[
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

            $chapter->update([
                'title' => $request->title,
                'content' => $request->content,
                'number' => $request->number,
                'status' => $request->status,
                'is_free' => $isFree,
                'price' => $price,
                'slug' => $chapter->id . '-chuong' . $request->number
            ]);

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
        return view('admin.pages.chapters.show', compact('story', 'chapter'));
    }

    public function destroy(Story $story, Chapter $chapter)
    {
        try {
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
}

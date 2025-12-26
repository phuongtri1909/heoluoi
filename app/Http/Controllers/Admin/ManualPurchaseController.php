<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoryPurchase;
use App\Models\ChapterPurchase;
use App\Models\User;
use App\Models\Story;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManualPurchaseController extends Controller
{
    /**
     * Display a listing of admin-added purchases.
     */
    public function index(Request $request)
    {
        // Only show purchases that have admin_id (manually added by admin)
        $storyPurchasesQuery = StoryPurchase::with(['user', 'admin', 'story'])
            ->whereNotNull('admin_id')
            ->latest();

        $chapterPurchasesQuery = ChapterPurchase::with(['user', 'admin', 'chapter.story'])
            ->whereNotNull('admin_id')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $storyPurchasesQuery->whereHas('user', function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
            
            $chapterPurchasesQuery->whereHas('user', function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $storyPurchases = $storyPurchasesQuery->paginate(15);
        $chapterPurchases = $chapterPurchasesQuery->paginate(15);

        return view('admin.pages.manual-purchases.index', [
            'storyPurchases' => $storyPurchases,
            'chapterPurchases' => $chapterPurchases,
            'search' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.manual-purchases.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation
        $validatedData = $request->validate([
            'user_ids' => 'required',
            'story_ids' => 'required',
            'reference_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Normalize IDs to arrays
        $userIds = is_array($request->user_ids) ? $request->user_ids : [$request->user_ids];
        $storyIds = is_array($request->story_ids) ? $request->story_ids : [$request->story_ids];
        
        if (empty($storyIds)) {
            return back()->withErrors(['story_ids' => 'Vui lòng chọn ít nhất 1 truyện']);
        }
        
        // Validate user existence
        foreach ($userIds as $userId) {
            if (!User::find($userId)) {
                return back()->withErrors(['user_ids' => 'Người dùng không tồn tại']);
            }
        }
        
        // Validate story existence
        foreach ($storyIds as $storyId) {
            if (!Story::find($storyId)) {
                return back()->withErrors(['story_ids' => 'Truyện không tồn tại']);
            }
        }

        $createdCount = 0;
        $errors = [];

        foreach ($userIds as $userId) {
            foreach ($storyIds as $storyId) {
                if (StoryPurchase::hasUserPurchased($userId, $storyId)) {
                    $user = User::find($userId);
                    $story = Story::find($storyId);
                    $errors[] = "{$user->email} đã mua truyện '{$story->title}'";
                    continue;
                }

                $story = Story::find($storyId);
                
                if ($story->combo_price <= 0) {
                    $errors[] = "Truyện '{$story->title}' không phải truyện trả phí";
                    continue;
                }
                
                StoryPurchase::create([
                    'user_id' => $userId,
                    'story_id' => $storyId,
                    'amount_paid' => 0,
                    'amount_received' => $story->combo_price ?? 0,
                    'admin_id' => Auth::id(),
                    'reference_id' => $request->reference_id,
                    'notes' => $request->notes,
                    'added_by' => 'admin',
                ]);
                
                $createdCount++;
            }
        }

        if (!empty($errors)) {
            if ($createdCount > 0) {
                return back()->with('warning', "Đã tạo $createdCount quyền truy cập thành công. Tuy nhiên có một số lỗi: " . implode(', ', array_slice($errors, 0, 5)));
            } else {
                return back()->withErrors(['error' => implode(', ', array_slice($errors, 0, 5))]);
            }
        }

        return redirect()->route('admin.manual-purchases.index')
                        ->with('success', 'Đã thêm quyền truy cập cho người dùng thành công');
    }

    /**
     * Remove story purchase.
     */
    public function destroyStoryPurchase(StoryPurchase $storyPurchase)
    {
        if ($storyPurchase->added_by !== 'admin') {
            return back()->withErrors(['error' => 'Không thể xóa purchase do user mua']);
        }
        
        $storyPurchase->delete();

        return redirect()->route('admin.manual-purchases.index')
                        ->with('success', 'Đã xóa quyền truy cập truyện thành công');
    }

    /**
     * Remove chapter purchase.
     */
    public function destroyChapterPurchase(ChapterPurchase $chapterPurchase)
    {
        if ($chapterPurchase->added_by !== 'admin') {
            return back()->withErrors(['error' => 'Không thể xóa purchase do user mua']);
        }
        
        $chapterPurchase->delete();

        return redirect()->route('admin.manual-purchases.index')
                        ->with('success', 'Đã xóa quyền truy cập chương thành công');
    }

    /**
     * Get stories for AJAX
     */
    public function getStories(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = Story::where('combo_price', '>', 0); // Only paid stories
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }
        
        $stories = $query->select('id', 'title', 'slug', 'author_name', 'combo_price')
                       ->orderBy('title')
                       ->limit(20)
                       ->get();

        return response()->json($stories);
    }

    /**
     * Get chapters for AJAX
     */
    public function getChapters(Request $request)
    {
        $search = $request->search;
        
        $chapters = Chapter::where('price', '>', 0) // Only paid chapters
                          ->where(function($query) use ($search) {
                              $query->where('title', 'like', "%{$search}%")
                                   ->orWhere('chapter_number', 'like', "%{$search}%");
                          })
                          ->with('story:id,title,slug')
                          ->select('id', 'story_id', 'title', 'chapter_number', 'price')
                          ->limit(10)
                          ->get();

        return response()->json($chapters);
    }

    /**
     * Get users for AJAX
     */
    public function getUsers(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = User::where('role', 'user');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $users = $query->select('id', 'email', 'name')
                    ->orderBy('email')
                    ->limit(20)
                    ->get();

        return response()->json($users);
    }
}

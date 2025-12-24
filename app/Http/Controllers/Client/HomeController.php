<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Story;
use App\Models\Banner;
use App\Models\Rating;
use App\Models\Status;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Socials;
use App\Models\Category;
use App\Models\UserReading;
use App\Constants\CacheKeys;
use Illuminate\Http\Request;
use App\Models\StoryPurchase;
use App\Models\ChapterPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\ReadingHistoryService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function searchHeader(Request $request)
    {
        $query = trim((string) $request->input('query'));

        $storiesQuery = Story::query()
            ->published()
            ->hide18Plus()
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('author_name', 'LIKE', "%{$query}%");
            })
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount(['bookmarks'])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating');

        // Apply advanced search filters (excluding query since it's already applied above)
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request, false);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => $query,
            'searchQuery' => $query,
            'displayQuery' => $query,
            'isSearch' => true,
            'searchType' => 'general',
            'searchUrl' => route('searchHeader'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function searchAuthor(Request $request)
    {
        $query = $request->input('query');

        // Search in stories by author name
        $storiesQuery = Story::query()
            ->published()
            ->hide18Plus()
            ->where('author_name', 'LIKE', "%{$query}%")
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount(['bookmarks'])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => $query,
            'searchQuery' => $query,
            'displayQuery' => $query,
            'isSearch' => true,
            'searchType' => 'author',
            'searchUrl' => route('search.author'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function searchTranslator(Request $request)
    {
        $query = $request->input('query');

        // Search in stories by translator name
        $storiesQuery = Story::query()
            ->published()
            ->hide18Plus()
            ->where(function ($outer) use ($query) {
                $outer->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                });
            })
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount(['bookmarks'])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating');

        // Apply advanced search filters (skip query filter since we already applied translator search)
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request, true);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => $query,
            'searchQuery' => $query,
            'displayQuery' => $query,
            'isSearch' => true,
            'searchType' => 'translator',
            'searchUrl' => route('search.translator'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showStoryCategories(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $storiesQuery = $category->stories()
            ->published()
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount(['bookmarks'])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'currentCategory' => $category,
            'query' => 'category',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('categories.story.show', $slug),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showStoryHot(Request $request)
    {
        $hasActiveFilters = ($request->filled('query') && trim($request->input('query')) !== '') ||
            ($request->filled('category') && trim($request->input('category')) !== '') ||
            ($request->filled('sort') && trim($request->input('sort')) !== '') ||
            ($request->filled('chapters') && trim($request->input('chapters')) !== '') ||
            ($request->filled('status') && trim($request->input('status')) !== '');

        if ($hasActiveFilters) {
            $storiesQuery = Story::query()
                ->published()
                ->hide18Plus()
                ->where('is_featured', true)
                ->whereHas('chapters', function ($query) {
                    $query->where('status', 'published');
                })
                ->with([
                    'categories',
                    'chapters' => function ($query) {
                        $query->select('id', 'story_id', 'views')
                            ->where('status', 'published');
                    },
                    'latestChapter' => function ($query) {
                        $query->select('id', 'story_id', 'number', 'created_at')
                            ->where('status', 'published');
                    }
                ])
                ->withCount(['bookmarks'])
                ->withSum('chapters', 'views')
                ->withAvg('ratings as average_rating', 'rating');

            $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

            $stories = $storiesQuery->paginate(20);
        } else {
            $stories = $this->getFeaturedStoriesForPage();

            $perPage = 20;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $stories = new LengthAwarePaginator(
                $stories->forPage($currentPage, $perPage),
                $stories->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'hot',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.hot'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    /**
     * Lấy truyện đề cử cho trang hot (không giới hạn số lượng)
     */
    private function getFeaturedStoriesForPage()
    {
        $query = Story::with([
            'categories',
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'views', 'created_at')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'created_at')
                    ->where('status', 'published');
            }
        ])
            ->published()
            ->hide18Plus()
            ->where('is_featured', true)
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'completed',
                'description',
                'created_at',
                'updated_at',
                'cover',
                'author_name',
                'is_featured',
                'featured_order'
            ])
            ->withCount([
                'chapters' => fn($q) => $q->where('status', 'published'),
                'storyPurchases',
                'chapterPurchases',
                'ratings',
                'bookmarks'
            ])
            ->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.story_id', 'stories.id');
            }, 'average_rating')
            ->withSum('chapters', 'views')
            ->orderBy('featured_order', 'asc')
            ->orderBy('created_at', 'desc');

        $stories = $query->get();

        // Thêm hot_score
        $stories = $stories->map(function ($story) {
            $story->hot_score = $this->calculateHotScore($story);
            return $story;
        });

        return $stories;
    }

    /**
     * Lấy truyện hot theo thuật toán cho trang hot (fallback)
     */
    private function getHotStoriesForPage()
    {
        $query = Story::with([
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'views', 'created_at')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'created_at')
                    ->where('status', 'published');
            }
        ])
            ->published()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'completed',
                'description',
                'created_at',
                'updated_at',
                'cover',
                'author_name'
            ])
            ->withCount([
                'chapters' => fn($q) => $q->where('status', 'published'),
                'storyPurchases',
                'chapterPurchases',
                'ratings',
                'bookmarks'
            ])
            ->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.story_id', 'stories.id');
            }, 'average_rating')
            ->where('updated_at', '>=', now()->subDays(30));

        $stories = $query->get()
            ->map(function ($story) {
                $story->hot_score = $this->calculateHotScore($story);
                return $story;
            })
            ->sortByDesc('hot_score')
            ->values();

        return $stories;
    }

    public function showRatingStories(Request $request)
    {
        $storiesQuery = Story::select('stories.*')
            ->where('status', 'published')
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->withAvg('ratings as average_rating', 'rating')
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount(['bookmarks'])
            ->withSum('chapters', 'views')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ratings')
                    ->whereColumn('ratings.story_id', 'stories.id');
            })
            ->orderByDesc('average_rating')
            ->orderBy('stories.created_at', 'ASC');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'rating',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.rating'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showStoryNewChapter(Request $request)
    {
        // Get latest chapter information using a subquery
        $latestChapters = DB::table('chapters')
            ->select(
                'story_id',
                DB::raw('MAX(COALESCE(published_at, created_at)) as latest_chapter_time')
            )
            ->where('status', 'published')
            ->groupBy('story_id');

        // Use withSubquery to avoid GROUP BY issues
        $storiesQuery = Story::select('stories.*')
            ->where('stories.status', 'published')
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->withAvg('ratings as average_rating', 'rating')
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'slug', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount(['bookmarks'])
            ->withSum('chapters', 'views')
            ->joinSub($latestChapters, 'latest_chapters', function ($join) {
                $join->on('stories.id', '=', 'latest_chapters.story_id');
            })
            ->orderByDesc('average_rating')
            ->orderByDesc('latest_chapters.latest_chapter_time');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'new-chapter',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.new.chapter'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showStoryNew(Request $request)
    {
        $query = Story::with([
            'categories',
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'views')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'title', 'slug', 'number', 'views', 'created_at', 'status')
                    ->where('status', 'published');
            }
        ])
            ->published()
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'status',
                'completed',
                'author_name',
                'description'
            ])
            ->withCount(['chapters' => function ($query) {
                $query->where('status', 'published');
            }, 'bookmarks'])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating')
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published')
                    ->whereMonth('published_at', now()->month)
                    ->whereYear('published_at', now()->year);
            })
            ->orderByDesc('created_at');

        // Apply advanced search filters
        $query = $this->applyAdvancedFilters($query, $request);

        $stories = $query->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'new',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.new'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showStoryView(Request $request)
    {
        $storyViews = DB::table('chapters')
            ->select('story_id', DB::raw('SUM(views) as total_views'))
            ->where('status', 'published')
            ->groupBy('story_id');

        $storiesQuery = Story::select('stories.*')
            ->where('stories.status', 'published')
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withCount([
                'bookmarks',
                'chapters' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->withAvg('ratings as average_rating', 'rating')
            ->joinSub($storyViews, 'story_views', function ($join) {
                $join->on('stories.id', '=', 'story_views.story_id');
            })
            ->addSelect('story_views.total_views')
            ->orderByDesc('story_views.total_views');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'view',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.view'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showStoryFollow(Request $request)
    {
        $storiesQuery = Story::withCount([
            'bookmarks',
            'chapters' => function ($query) {
                $query->where('status', 'published');
            }
        ])
            ->published()
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->with([
                'categories',
                'chapters' => function ($query) {
                    $query->select('id', 'story_id', 'views')
                        ->where('status', 'published');
                },
                'latestChapter' => function ($query) {
                    $query->select('id', 'story_id', 'number', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating')
            ->orderByDesc('bookmarks_count');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'follow',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.follow'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function showCompletedStories(Request $request)
    {
        $storiesQuery = Story::with([
            'categories',
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'price', 'is_free', 'views')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'created_at')
                    ->where('status', 'published');
            }
        ])
            ->published()
            ->hide18Plus()
            ->where('completed', true)
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'completed',
                'updated_at',
                'author_name',
                'description',
            ])
            ->withCount(['chapters' => function ($query) {
                $query->where('status', 'published');
            }, 'bookmarks'])
            ->withSum('chapters', 'views')
            ->withAvg('ratings as average_rating', 'rating')
            ->latest('updated_at');

        // Apply advanced search filters
        $storiesQuery = $this->applyAdvancedFilters($storiesQuery, $request);

        $stories = $storiesQuery->paginate(20);

        return view('pages.search.results', [
            'stories' => $stories,
            'query' => 'completed',
            'searchQuery' => $request->input('query', ''),
            'displayQuery' => $request->input('query', ''),
            'isSearch' => false,
            'searchUrl' => route('story.completed'),
            'categories' => Category::orderBy('name')->get()
        ]);
    }

    public function index(Request $request)
    {
        // Cache key với category_id nếu có
        $cacheKey = 'home_data_' . ($request->category_id ?? 'all');
        
        // Cache toàn bộ data trong 5 phút để giảm tải database
        $data = Cache::remember($cacheKey, 300, function () use ($request) {
            return $this->loadHomeData($request);
        });

        // Currently reading không cache vì phụ thuộc vào user
        $data['currentlyReading'] = $this->getCurrentlyReading();

        if ($request->ajax()) {
            if ($request->type === 'hot') {
                return response()->json([
                    'html' => view('components.stories-grid', ['hotStories' => $data['hotStories']])->render()
                ]);
            } elseif ($request->type === 'new') {
                return response()->json([
                    'html' => view('components.story-list-items', ['newStories' => $data['newStories']])->render()
                ]);
            }
        }

        return view('pages.home', $data);
    }

    /**
     * Load tất cả data cho trang home - tối ưu query bằng cách batch load
     */
    private function loadHomeData($request)
    {
        // Load tất cả stories cần thiết trong một batch query lớn
        $allStoryIds = collect();
        
        // 1. Featured stories (hot) - 12 stories
        $featuredIds = Story::published()
            ->hide18Plus()
            ->where('is_featured', true)
            ->whereHas('chapters', function ($q) {
                $q->where('status', 'published');
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->whereHas('categories', function ($cq) use ($request) {
                    $cq->where('categories.id', $request->category_id);
                });
            })
            ->orderBy('featured_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->pluck('id');
        
        $allStoryIds = $allStoryIds->merge($featuredIds);

        // 2. New stories - 20 stories (created trong tháng)
        $newIds = Story::published()
            ->hide18Plus()
            ->whereHas('chapters', function ($q) {
                $q->where('status', 'published')
                    ->where('published_at', '>=', now()->subMonth());
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->pluck('id');
        
        $allStoryIds = $allStoryIds->merge($newIds);

        // 3. Rating stories - 10 stories
        $ratingIds = Story::published()
            ->hide18Plus()
            ->whereHas('chapters', function ($q) {
                $q->where('status', 'published');
            })
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('ratings')
                    ->whereColumn('ratings.story_id', 'stories.id');
            })
            ->orderByDesc(DB::raw('(SELECT AVG(rating) FROM ratings WHERE ratings.story_id = stories.id)'))
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->pluck('id');
        
        $allStoryIds = $allStoryIds->merge($ratingIds);

        // 4. Latest updated - 10 stories với story_id và latest_time để sort sau
        $latestChaptersData = DB::table('chapters')
            ->select('story_id', DB::raw('MAX(created_at) as latest_time'))
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subMonth())
            ->groupBy('story_id')
            ->orderByDesc('latest_time')
            ->limit(10)
            ->get();
        
        $latestChapterIds = $latestChaptersData->pluck('story_id');
        $latestChapterTimes = $latestChaptersData->keyBy('story_id');
        $allStoryIds = $allStoryIds->merge($latestChapterIds);

        // 5. Top viewed - 10 stories với total_views để sort sau
        $topViewedData = DB::table('chapters')
            ->select('story_id', DB::raw('SUM(views) as total_views'))
            ->where('status', 'published')
            ->groupBy('story_id')
            ->having('total_views', '>', 0)
            ->orderByDesc('total_views')
            ->limit(10)
            ->get();
        
        $topViewedIds = $topViewedData->pluck('story_id');
        $topViewedTotals = $topViewedData->keyBy('story_id');
        
        $allStoryIds = $allStoryIds->merge($topViewedIds);

        // 6. Top followed - 10 stories (cần load riêng để có bookmarks_count)
        $topFollowedData = Story::published()
            ->hide18Plus()
            ->whereHas('chapters', function ($q) {
                $q->where('status', 'published');
            })
            ->withCount('bookmarks')
            ->having('bookmarks_count', '>', 0)
            ->orderByDesc('bookmarks_count')
            ->limit(10)
            ->get(['id', 'bookmarks_count'])
            ->keyBy('id');
        
        $topFollowedIds = $topFollowedData->pluck('id');
        $allStoryIds = $allStoryIds->merge($topFollowedIds);

        // 7. Completed stories - không giới hạn nhưng chỉ lấy một số
        $completedIds = Story::published()
            ->hide18Plus()
            ->where('completed', true)
            ->whereHas('chapters', function ($q) {
                $q->where('status', 'published');
            })
            ->latest('updated_at')
            ->limit(20)
            ->pluck('id');
        
        $allStoryIds = $allStoryIds->merge($completedIds);

        // Loại bỏ duplicate và load tất cả trong một query duy nhất
        $uniqueIds = $allStoryIds->unique()->values();
        
        if ($uniqueIds->isEmpty()) {
            return [
                'hotStories' => collect(),
                'newStories' => collect(),
                'ratingStories' => collect(),
                'latestUpdatedStories' => collect(),
                'topViewedStories' => collect(),
                'topFollowedStories' => collect(),
                'completedStories' => collect(),
            ];
        }

        // Load tất cả stories với tất cả relationships và computed fields trong một query
        $allStories = Story::whereIn('id', $uniqueIds)
            ->published()
            ->hide18Plus()
            ->with([
                'categories:id,name,slug,is_main',
                'latestChapter' => function ($q) {
                    $q->select('id', 'story_id', 'number', 'slug', 'title', 'created_at')
                        ->where('status', 'published');
                }
            ])
            ->select([
                'id', 'title', 'slug', 'cover', 'completed', 'is_18_plus',
                'author_name', 'description', 'created_at', 'updated_at',
                'is_featured', 'featured_order', 'has_combo', 'combo_price'
            ])
            ->withCount([
                'chapters' => fn($q) => $q->where('status', 'published'),
                'chapters as vip_chapters_count' => fn($q) => $q->where('status', 'published')->where('is_free', 0),
                'bookmarks',
                'ratings'
            ])
            ->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.story_id', 'stories.id');
            }, 'average_rating')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published')
                    ->where('is_free', 0);
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views')
            ->get()
            ->keyBy('id');

        // Phân loại và sort stories theo từng danh sách
        $hotStories = $featuredIds->map(fn($id) => $allStories->get($id))
            ->filter()
            ->sortBy([['featured_order', 'asc'], ['created_at', 'desc']])
            ->take(12)
            ->values();

        $newStories = $newIds->map(fn($id) => $allStories->get($id))
            ->filter()
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        $ratingStories = $ratingIds->map(fn($id) => $allStories->get($id))
            ->filter()
            ->sortByDesc('average_rating')
            ->sortBy('created_at')
            ->take(10)
            ->values();

        $latestUpdatedStories = $latestChapterIds->map(function ($id) use ($allStories, $latestChapterTimes) {
                $story = $allStories->get($id);
                if ($story && isset($latestChapterTimes[$id])) {
                    $story->latest_chapter_time = $latestChapterTimes[$id]->latest_time;
                }
                return $story;
            })
            ->filter()
            ->sortByDesc('latest_chapter_time')
            ->take(10)
            ->values();

        $topViewedStories = $topViewedIds->map(fn($id) => $allStories->get($id))
            ->filter()
            ->sortByDesc('total_views')
            ->take(10)
            ->values();

        $topFollowedStories = $topFollowedIds->map(function ($id) use ($allStories, $topFollowedData) {
                $story = $allStories->get($id);
                if ($story && isset($topFollowedData[$id])) {
                    // Đảm bảo bookmarks_count được set đúng
                    $story->bookmarks_count = $topFollowedData[$id]->bookmarks_count;
                }
                return $story;
            })
            ->filter()
            ->sortByDesc('bookmarks_count')
            ->take(10)
            ->values();

        $completedStories = $completedIds->map(fn($id) => $allStories->get($id))
            ->filter()
            ->sortByDesc('updated_at')
            ->take(20)
            ->values();

        return [
            'hotStories' => $hotStories,
            'newStories' => $newStories,
            'ratingStories' => $ratingStories,
            'latestUpdatedStories' => $latestUpdatedStories,
            'topViewedStories' => $topViewedStories,
            'topFollowedStories' => $topFollowedStories,
            'completedStories' => $completedStories,
        ];
    }

    private function getCurrentlyReading()
    {
        if (!Auth::check()) {
            return collect();
        }

        return UserReading::with([
            'story' => function ($query) {
                $query->select('id', 'title', 'slug', 'cover');
            },
            'chapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'slug');
            }
        ])
            ->where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();
    }

    private function getCompletedStories()
    {
        return Story::with([
            'categories',
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'price', 'is_free', 'views')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'created_at')
                    ->where('status', 'published');
            }
        ])
            ->published()
            ->hide18Plus()
            ->where('completed', true)
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'is_18_plus',
                'completed',
                'updated_at',
                'author_name',
                'has_combo',
                'description',
                'combo_price'
            ])
            ->withCount([
                'chapters' => function ($query) {
                    $query->where('status', 'published');
                },
                'chapters as vip_chapters_count' => function ($query) {
                    $query->where('status', 'published')->where('is_free', 0);
                }
            ])
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published')
                    ->where('is_free', 0);
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views')
            ->withAvg('ratings as average_rating', 'rating')
            ->latest('updated_at')
            ->get();
    }


    private function getHotStories($request)
    {
        $featuredStories = $this->getFeaturedStories($request);
        return $featuredStories;
    }

    /**
     * Lấy truyện đề cử theo featured_order
     */
    private function getFeaturedStories($request)
    {
        $query = Story::with([
            'categories',
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'views', 'created_at', 'price', 'is_free')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'created_at')
                    ->where('status', 'published');
            },
        ])
            ->published()
            ->hide18Plus()
            ->where(function ($q) {
                $q->where('is_featured', true);
            })
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'completed',
                'is_18_plus',
                'description',
                'created_at',
                'updated_at',
                'author_name',
                'is_featured',
                'featured_order'
            ])
            ->withCount([
                'chapters' => fn($q) => $q->where('status', 'published'),
                'chapters as vip_chapters_count' => fn($q) => $q->where('status', 'published')->where('is_free', 0),
                'storyPurchases',
                'chapterPurchases',
                'ratings',
                'bookmarks'
            ])
            ->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.story_id', 'stories.id');
            }, 'average_rating')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published')
                    ->where('is_free', 0);
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views');

        if ($request && $request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $featuredStories = $query
            ->orderBy('is_featured', 'desc')
            ->orderBy('featured_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();


        return $featuredStories;
    }

    /**
     * Lấy truyện hot theo thuật toán tính điểm (fallback khi không có featured)
     */
    private function getHotStoriesByScore($request)
    {
        $query = Story::with([
            'chapters' => function ($query) {
                $query->select('id', 'story_id', 'views', 'created_at')
                    ->where('status', 'published');
            },
            'latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'created_at')
                    ->where('status', 'published');
            }
        ])
            ->published()
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->select([
                'id',
                'title',
                'slug',
                'cover',
                'completed',
                'is_18_plus',
                'description',
                'created_at',
                'updated_at',
                'author_name'
            ])
            ->withCount([
                'chapters' => fn($q) => $q->where('status', 'published'),
                'storyPurchases',
                'chapterPurchases',
                'ratings',
                'bookmarks'
            ])
            ->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.story_id', 'stories.id');
            }, 'average_rating')
            ->where('updated_at', '>=', now()->subDays(30));

        // Filter by category if requested
        if ($request && $request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Tính hot score và sắp xếp
        $hotStories = $query->get()
            ->map(function ($story) {
                $story->hot_score = $this->calculateHotScore($story);
                return $story;
            })
            ->sortByDesc('hot_score')
            ->take(12);

        return $hotStories;
    }

    private function calculateHotScore($story)
    {
        return ($story->story_purchases_count * 3) +
            ($story->chapter_purchases_count * 2) +
            ($story->ratings_count * 1.5) +
            ($story->average_rating * 2) +
            ($story->bookmarks_count * 1);
    }

    private function getNewStories()
    {
        $query = Story::with(['latestChapter' => function ($query) {
            $query->select('id', 'story_id', 'title', 'slug', 'number', 'views', 'created_at', 'status')
                ->where('status', 'published');
        }, 'categories', 'user:id,name'])
            ->published()
            ->hide18Plus()
            ->select([
                'id',
                'user_id',
                'title',
                'slug',
                'cover',
                'status',
                'completed',
                'is_18_plus',
                'author_name',
                'description'
            ])
            ->withCount([
                'chapters' => function ($query) {
                    $query->where('status', 'published');
                },
                'chapters as vip_chapters_count' => function ($query) {
                    $query->where('status', 'published')->where('is_free', 0);
                }
            ])
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published')
                    ->where('is_free', 0);
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views')
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '>=', now()->subMonth());
            });

        return $query->orderByDesc('created_at')
            ->take(20)
            ->get();
    }


    public function latestUpdatedStories()
    {
        // Get latest chapter information using a subquery
        $latestChapters = DB::table('chapters')
            ->select(
                'story_id',
                DB::raw('MAX(created_at) as latest_chapter_time'),
                DB::raw('COUNT(*) as chapters_count')
            )
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subMonth())
            ->groupBy('story_id');

        return Story::select(
            'stories.id',
            'stories.title',
            'stories.user_id',
            'stories.status',
            'stories.slug',
            'stories.is_18_plus',
            'stories.created_at',
            'stories.updated_at',
            'stories.cover',
            'stories.cover_thumbnail',
            'stories.author_name',
            'stories.completed'
        )
            ->where('stories.status', 'published')
            ->hide18Plus()
            ->withAvg('ratings as average_rating', 'rating')
            ->with(['latestChapter' => function ($query) {
                $query->select('id', 'story_id', 'number', 'slug', 'created_at','title')
                    ->where('status', 'published');
            }])
            ->joinSub($latestChapters, 'latest_chapters', function ($join) {
                $join->on('stories.id', '=', 'latest_chapters.story_id');
            })
            ->addSelect('latest_chapters.chapters_count', 'latest_chapters.latest_chapter_time')
            ->orderByDesc('latest_chapters.latest_chapter_time')
            ->limit(10)
            ->get();
    }

    public function getRatingStories()
    {
        return Story::select([
            'id',
            'title',
            'slug',
            'description',
            'cover',
            'author_name',
            'completed',
            'is_18_plus',
            'created_at',
            'updated_at'
        ])
            ->where('status', 'published')
            ->hide18Plus()
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ratings')
                    ->whereColumn('ratings.story_id', 'stories.id');
            })
            ->withAvg('ratings as average_rating', 'rating')
            ->withCount([
                'chapters' => function ($query) {
                    $query->where('status', 'published');
                },
                'chapters as vip_chapters_count' => function ($query) {
                    $query->where('status', 'published')->where('is_free', 0);
                }
            ])
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published')
                    ->where('is_free', 0);
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views')
            ->orderByDesc('average_rating')
            ->orderBy('stories.created_at', 'ASC')
            ->limit(10)
            ->get();
    }


    public function topViewedStories()
    {
        $storyViews = DB::table('chapters')
            ->select('story_id', DB::raw('SUM(views) as total_views'))
            ->where('status', 'published')
            ->groupBy('story_id')
            ->having('total_views', '>', 0);

        return Story::select([
            'stories.id',
            'stories.title',
            'stories.slug',
            'stories.cover',
            'stories.author_name',
            'stories.completed',
            'stories.is_18_plus',
            'stories.created_at',
            'stories.updated_at'
        ])
            ->where('stories.status', 'published')
            ->hide18Plus()
            ->withCount([
                'chapters' => function ($query) {
                    $query->where('status', 'published');
                },
                'chapters as vip_chapters_count' => function ($query) {
                    $query->where('status', 'published')->where('is_free', 0);
                }
            ])
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published')
                    ->where('is_free', 0);
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views')
            ->joinSub($storyViews, 'story_views', function ($join) {
                $join->on('stories.id', '=', 'story_views.story_id');
            })
            ->addSelect('story_views.total_views')
            ->orderByDesc('story_views.total_views')
            ->limit(10)
            ->get();
    }

    public function topFollowedStories()
    {
        return Story::select([
            'id',
            'title',
            'slug',
            'cover',
            'author_name',
            'completed',
            'is_18_plus',
            'created_at',
            'updated_at'
        ])
            ->where('status', 'published')
            ->hide18Plus()
            ->withCount([
                'bookmarks',
                'chapters' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->having('bookmarks_count', '>', 0)
            ->orderByDesc('bookmarks_count')
            ->limit(10)
            ->get();
    }

    public function showStory(Request $request, $slug)
    {
        // Cache key cho story data (không cache user-specific data)
        $cacheKey = 'story_data_' . $slug;
        
        // Cache story data trong 5 phút
        $data = Cache::remember($cacheKey, 300, function () use ($slug, $request) {
            return $this->loadStoryData($slug, $request);
        });

        // Cache chapters list (không user-specific) - cache 5 phút
        $chaptersCacheKey = 'story_chapters_' . $data['story']->id . '_page_' . request()->get('page', 1);
        $chapters = Cache::remember($chaptersCacheKey, 300, function () use ($data) {
            return Chapter::where('story_id', $data['story']->id)
                ->published()
                ->select('id', 'story_id', 'number', 'slug', 'title', 'price', 'is_free', 'status', 'views', 'created_at')
                ->orderBy('number', 'asc')
                ->paginate(50);
        });

        // Load user-specific data: chapter purchases, bookmark, rating, story purchase
        $chapterPurchaseStatus = [];
        $isBookmarked = false;
        $userRating = null;
        $hasPurchasedStory = false;
        
        if (Auth::check()) {
            $userId = Auth::id();
            $storyId = $data['story']->id;
            
            // Batch check: chapter purchases (bỏ duplicate query 2)
            $chapterIds = $chapters->pluck('id');
            if ($chapterIds->isNotEmpty()) {
                $purchasedChapterIds = ChapterPurchase::whereIn('chapter_id', $chapterIds)
                    ->where('user_id', $userId)
                    ->pluck('chapter_id')
                    ->toArray();
                
                foreach ($chapterIds as $chapterId) {
                    $chapterPurchaseStatus[$chapterId] = in_array($chapterId, $purchasedChapterIds);
                }
            }
            
            // Batch check: bookmark, rating, story purchase trong 1 query
            $bookmark = \App\Models\Bookmark::where('user_id', $userId)
                ->where('story_id', $storyId)
                ->select('id')
                ->first();
            $isBookmarked = $bookmark !== null;
            
            $userRating = \App\Models\Rating::where('user_id', $userId)
                ->where('story_id', $storyId)
                ->select('rating')
                ->first();
            
            $hasPurchasedStory = StoryPurchase::where('user_id', $userId)
                ->where('story_id', $storyId)
                ->select('id')
                ->exists();
        }

        // Cache comments - cache 2 phút để giảm queries
        $commentsCacheKey = 'story_comments_' . $data['story']->id . '_page_' . request()->get('page', 1);
        $commentsData = Cache::remember($commentsCacheKey, 120, function () use ($data) {
            $pinnedComments = Comment::with([
                'user:id,name,avatar,role',
                'approvedReplies' => function ($q) {
                    $q->select('id', 'user_id', 'comment', 'reply_id', 'created_at')
                        ->approved()
                        ->latest();
                },
                'approvedReplies.user:id,name,avatar,role',
                'reactions:id,comment_id,user_id,type'
            ])
                ->where('story_id', $data['story']->id)
                ->whereNull('reply_id')
                ->where('is_pinned', true)
                ->approved()
                ->select('id', 'user_id', 'comment', 'story_id', 'reply_id', 'is_pinned', 'pinned_at', 'created_at')
                ->latest('pinned_at')
                ->get();

            $regularComments = Comment::with([
                'user:id,name,avatar,role',
                'approvedReplies' => function ($q) {
                    $q->select('id', 'user_id', 'comment', 'reply_id', 'created_at')
                        ->approved()
                        ->latest();
                },
                'approvedReplies.user:id,name,avatar,role',
                'reactions:id,comment_id,user_id,type'
            ])
                ->where('story_id', $data['story']->id)
                ->whereNull('reply_id')
                ->where('is_pinned', false)
                ->approved()
                ->select('id', 'user_id', 'comment', 'story_id', 'reply_id', 'is_pinned', 'created_at')
                ->latest()
                ->paginate(10);
            
            return [
                'pinned' => $pinnedComments,
                'regular' => $regularComments
            ];
        });
        
        $pinnedComments = $commentsData['pinned'];
        $regularComments = $commentsData['regular'];

        return view('pages.story', array_merge($data, [
            'chapters' => $chapters,
            'pinnedComments' => $pinnedComments,
            'regularComments' => $regularComments,
            'chapterPurchaseStatus' => $chapterPurchaseStatus,
            'isBookmarked' => $isBookmarked,
            'userRating' => $userRating ? $userRating->rating : 0,
            'hasPurchasedStory' => $hasPurchasedStory,
        ]));
    }

    /**
     * Load tất cả data cho trang story - tối ưu bằng batch loading
     */
    private function loadStoryData($slug, $request)
    {
        // Load story chính với tất cả computed fields
        $story = Story::where('slug', $slug)
            ->published()
            ->with([
                'categories' => function ($query) {
                    $query->select('categories.id', 'categories.name', 'categories.slug');
                },
                'user:id,name'
            ])
            ->select('stories.*')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(price)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_chapter_price')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('SUM(views)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_views')
            ->selectSub(function ($q) {
                $q->from('chapters')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('chapters.story_id', 'stories.id')
                    ->where('status', 'published');
            }, 'total_chapters')
            ->withCount([
                'bookmarks',
                'ratings'
            ])
            ->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.story_id', 'stories.id');
            }, 'average_rating')
            ->firstOrFail();

        // Tính stats từ computed fields
        $stats = [
            'total_chapters' => $story->total_chapters ?? 0,
            'total_views' => $story->total_views ?? 0,
            'total_bookmarks' => $story->bookmarks_count ?? 0,
            'ratings' => [
                'count' => $story->ratings_count ?? 0,
                'average' => $story->average_rating ?? 0
            ]
        ];

        $status = (object)[
            'status' => $story->completed ? 'done' : 'ongoing'
        ];

        $storyCategories = $story->categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
        });

        // Load latest chapters
        $latestChapters = Chapter::where('story_id', $story->id)
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subHours(24))
            ->select('id', 'story_id', 'number', 'title', 'slug', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Batch load tất cả related stories trong một query
        $allRelatedStoryIds = collect();
        $featuredIds = collect();
        $authorIds = collect();
        $translatorIds = collect();
        $relatedIds = collect();
        
        // Featured stories - 12 stories
        $featuredIds = Story::published()
            ->hide18Plus()
            ->where('is_featured', true)
            ->whereHas('chapters', function ($q) {
                $q->where('status', 'published');
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->whereHas('categories', function ($cq) use ($request) {
                    $cq->where('categories.id', $request->category_id);
                });
            })
            ->orderBy('featured_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->pluck('id');
        
        $allRelatedStoryIds = $allRelatedStoryIds->merge($featuredIds);

        // Author stories - 5 stories
        if ($story->author_name) {
            $authorIds = Story::published()
                ->hide18Plus()
                ->where('author_name', 'LIKE', "%{$story->author_name}%")
                ->where('id', '!=', $story->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->pluck('id');
            
            $allRelatedStoryIds = $allRelatedStoryIds->merge($authorIds);
        }

        // Translator stories - 5 stories
        if ($story->user_id) {
            $translatorIds = Story::published()
                ->hide18Plus()
                ->where('user_id', $story->user_id)
                ->where('id', '!=', $story->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->pluck('id');
            
            $allRelatedStoryIds = $allRelatedStoryIds->merge($translatorIds);
        }

        // Related stories by category - 8 stories
        $firstCategory = $story->categories->first();
        if ($firstCategory) {
            $relatedIds = Story::published()
                ->hide18Plus()
                ->whereHas('categories', function ($q) use ($firstCategory) {
                    $q->where('categories.id', $firstCategory->id);
                })
                ->where('id', '!=', $story->id)
                ->whereHas('chapters', function ($q) {
                    $q->where('status', 'published');
                })
                ->orderByDesc(DB::raw('(SELECT SUM(views) FROM chapters WHERE chapters.story_id = stories.id AND chapters.status = "published")'))
                ->limit(8)
                ->pluck('id');
            
            $allRelatedStoryIds = $allRelatedStoryIds->merge($relatedIds);
        }

        // Load tất cả related stories trong một query
        $uniqueRelatedIds = $allRelatedStoryIds->unique()->values();
        $allRelatedStories = collect();
        
        if ($uniqueRelatedIds->isNotEmpty()) {
            $allRelatedStories = Story::whereIn('id', $uniqueRelatedIds)
                ->published()
                ->hide18Plus()
                ->with([
                    'categories' => function ($q) {
                        $q->select('categories.id', 'categories.name', 'categories.slug');
                    },
                    'latestChapter' => function ($q) {
                        $q->select('id', 'story_id', 'number', 'slug', 'title', 'created_at')
                            ->where('status', 'published');
                    },
                    'user:id,name'
                ])
                ->select([
                    'id', 'title', 'slug', 'cover', 'completed', 'is_18_plus',
                    'author_name', 'description', 'created_at', 'updated_at',
                    'is_featured', 'featured_order', 'user_id'
                ])
                ->withCount([
                    'chapters' => fn($q) => $q->where('status', 'published'),
                ])
                ->selectSub(function ($q) {
                    $q->from('chapters')
                        ->selectRaw('SUM(views)')
                        ->whereColumn('chapters.story_id', 'stories.id')
                        ->where('status', 'published');
                }, 'total_views')
                ->get()
                ->keyBy('id');
        }

        // Phân loại stories theo từng danh sách
        $featuredStories = $featuredIds->map(fn($id) => $allRelatedStories->get($id))
            ->filter()
            ->sortBy([['featured_order', 'asc'], ['created_at', 'desc']])
            ->take(12)
            ->values();

        $authorStories = $authorIds->map(fn($id) => $allRelatedStories->get($id))
            ->filter()
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $translatorStories = $translatorIds->map(fn($id) => $allRelatedStories->get($id))
            ->filter()
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $relatedStories = $relatedIds->map(fn($id) => $allRelatedStories->get($id))
            ->filter()
            ->sortByDesc('total_views')
            ->take(8)
            ->values();

        return [
            'story' => $story,
            'stats' => $stats,
            'status' => $status,
            'storyCategories' => $storyCategories,
            'featuredStories' => $featuredStories,
            'authorStories' => $authorStories,
            'translatorStories' => $translatorStories,
            'latestChapters' => $latestChapters,
            'relatedStories' => $relatedStories,
        ];
    }

    private function getLatestChapters($storyId)
    {
        return Chapter::where('story_id', $storyId)
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subHours(24))
            ->select('id', 'story_id', 'number', 'title', 'slug', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
    }

    private function getRelatedStoriesByCategory($story)
    {
        // Lấy thể loại đầu tiên của truyện
        $firstCategory = $story->categories->first();
        
        if (!$firstCategory) {
            return collect();
        }

        return Story::whereHas('categories', function ($query) use ($firstCategory) {
                $query->where('categories.id', $firstCategory->id);
            })
            ->where('id', '!=', $story->id)
            ->where('status', 'published')
            ->hide18Plus()
            ->whereHas('chapters', function ($query) {
                $query->where('status', 'published');
            })
            ->withCount(['chapters' => function ($query) {
                $query->where('status', 'published');
            }])
            ->withSum('chapters', 'views')
            ->orderByDesc('chapters_sum_views')
            ->take(8)
            ->get();
    }

    public function getStoryChapters(Request $request, $storyId)
    {
        $story = Story::findOrFail($storyId);

        // Query base
        $chaptersQuery = Chapter::where('story_id', $storyId)
            ->published();

        // Sắp xếp theo thứ tự yêu cầu
        $sortOrder = $request->get('sort_order', 'asc');
        if ($sortOrder === 'asc') {
            $chaptersQuery->orderBy('number', 'asc');
        } else {
            $chaptersQuery->orderBy('number', 'desc');
        }

        // Tìm kiếm nếu có
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $searchNumber = preg_replace('/[^0-9]/', '', $search);

            $chaptersQuery->where(function ($q) use ($search, $searchNumber) {
                $q->where('title', 'like', "%{$search}%");

                if (!empty($searchNumber)) {
                    $q->orWhere('number', $searchNumber);
                }
            });
        }

        $chapters = $chaptersQuery->paginate(50);

        $chapterPurchaseStatus = [];
        if (Auth::check()) {
            $chapterIds = $chapters->pluck('id');
            $purchasedChapterIds = \App\Models\ChapterPurchase::whereIn('chapter_id', $chapterIds)
                ->where('user_id', Auth::id())
                ->pluck('chapter_id')
                ->toArray();

            foreach ($chapterIds as $chapterId) {
                $chapterPurchaseStatus[$chapterId] = in_array($chapterId, $purchasedChapterIds);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.chapter-items', [
                    'chapters' => $chapters,
                    'story' => $story,
                    'sortOrder' => $sortOrder,
                    'chapterPurchaseStatus' => $chapterPurchaseStatus
                ])->render(),
                'pagination' => view('components.pagination', ['paginator' => $chapters])->render()
            ]);
        }

        return redirect()->route('show.page.story', $story->slug);
    }


    public function chapterByStory($storySlug, $chapterSlug)
    {
        // Cache key cho chapter data (không cache user-specific data)
        $cacheKey = 'chapter_data_' . $storySlug . '_' . $chapterSlug;
        
        // Cache chapter data trong 5 phút
        $data = Cache::remember($cacheKey, 300, function () use ($storySlug, $chapterSlug) {
            return $this->loadChapterData($storySlug, $chapterSlug);
        });

        // Load user-specific data (không cache) - tối ưu batch loading
        $readingService = new ReadingHistoryService();
        $readingService->saveReadingProgress($data['story'], $data['chapter']);

        // Batch load reading progress và recent reads
        $userId = Auth::id();
        $storyId = $data['story']->id;
        $chapterId = $data['chapter']->id;
        
        if ($userId) {
            // Gộp 2 queries thành 1: load tất cả readings cùng lúc (lấy 10 records để có đủ cho recent reads)
            $allReadings = UserReading::where('user_id', $userId)
                ->select('id', 'story_id', 'chapter_id', 'progress_percent', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get();
            
            // Tách progress (current chapter) và recent reads (các chapters khác)
            $userReading = $allReadings->first(function ($r) use ($storyId, $chapterId) {
                return $r->story_id == $storyId && $r->chapter_id == $chapterId;
            });
            
            // Recent reads: loại trừ current chapter, lấy 5 records đầu tiên
            $recentReads = $allReadings
                ->reject(function ($r) use ($storyId, $chapterId) {
                    return $r->story_id == $storyId && $r->chapter_id == $chapterId;
                })
                ->take(5)
                ->values();
            
            // Chỉ eager load nếu có records - không load categories (không cần trong recent reads view)
            if ($recentReads->isNotEmpty()) {
                $recentReads->load([
                    'story:id,title,slug,cover',
                    'chapter:id,story_id,number,slug,title'
                ]);
            }
        } else {
            // Guest - gộp 2 queries thành 1
            $deviceKey = $readingService->getOrCreateDeviceKey();
            $allReadings = UserReading::where('session_id', $deviceKey)
                ->whereNull('user_id')
                ->select('id', 'story_id', 'chapter_id', 'progress_percent', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get();
            
            // Tách progress và recent reads
            $userReading = $allReadings->first(function ($r) use ($storyId, $chapterId) {
                return $r->story_id == $storyId && $r->chapter_id == $chapterId;
            });
            
            $recentReads = $allReadings
                ->reject(function ($r) use ($storyId, $chapterId) {
                    return $r->story_id == $storyId && $r->chapter_id == $chapterId;
                })
                ->take(5)
                ->values();
            
            // Chỉ eager load nếu có records - không load categories (không cần)
            if ($recentReads->isNotEmpty()) {
                $recentReads->load([
                    'story:id,title,slug,cover',
                    'chapter:id,story_id,number,slug,title'
                ]);
            }
        }

        $readingProgress = $userReading ? $userReading->progress_percent : 0;

        // Kiểm tra quyền truy cập nội dung (user-specific) - batch check
        $hasAccess = false;
        $hasPurchasedChapter = false;
        $hasPurchasedStory = false;

        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->role, ['admin_main', 'admin_sub'])) {
                $hasAccess = true;
            } else {
                // Batch check purchases - chỉ select id để tối ưu
                $hasPurchasedChapter = ChapterPurchase::where('user_id', $user->id)
                    ->where('chapter_id', $chapterId)
                    ->select('id')
                    ->exists();

                $hasPurchasedStory = StoryPurchase::where('user_id', $user->id)
                    ->where('story_id', $storyId)
                    ->select('id')
                    ->exists();

                $hasAccess = $hasPurchasedChapter || $hasPurchasedStory;
            }
        }

        if (!$data['chapter']->price || $data['chapter']->price == 0) {
            $hasAccess = true;
        }

        // Password feature không có trong database, bỏ qua check
        $hasPasswordAccess = true;

        // Xử lý content dựa trên access
        $chapter = $data['chapter'];
        if (!$hasAccess) {
            $originalContent = $chapter->content;
            $previewLength = min(300, intval(strlen($originalContent) * 0.1));
            $chapter->preview_content = substr($originalContent, 0, $previewLength) . '...';
        }

        if (!$hasAccess || !$hasPasswordAccess) {
            $chapter->content = '';
        }

        // Cache comments - cache 2 phút để giảm queries
        $commentsCacheKey = 'story_comments_' . $storyId . '_page_' . request()->get('page', 1);
        $commentsData = Cache::remember($commentsCacheKey, 120, function () use ($storyId) {
            $pinnedComments = Comment::with([
                'user:id,name,avatar,role',
                'approvedReplies' => function ($q) {
                    $q->select('id', 'user_id', 'comment', 'reply_id', 'created_at')
                        ->approved()
                        ->latest();
                },
                'approvedReplies.user:id,name,avatar,role',
                'reactions:id,comment_id,user_id,type'
            ])
                ->where('story_id', $storyId)
                ->whereNull('reply_id')
                ->where('is_pinned', true)
                ->approved()
                ->select('id', 'user_id', 'comment', 'story_id', 'reply_id', 'is_pinned', 'pinned_at', 'created_at')
                ->latest('pinned_at')
                ->get();

            $regularComments = Comment::with([
                'user:id,name,avatar,role',
                'approvedReplies' => function ($q) {
                    $q->select('id', 'user_id', 'comment', 'reply_id', 'created_at')
                        ->approved()
                        ->latest();
                },
                'approvedReplies.user:id,name,avatar,role',
                'reactions:id,comment_id,user_id,type'
            ])
                ->where('story_id', $storyId)
                ->whereNull('reply_id')
                ->where('is_pinned', false)
                ->approved()
                ->select('id', 'user_id', 'comment', 'story_id', 'reply_id', 'is_pinned', 'created_at')
                ->latest()
                ->paginate(10);
            
            return [
                'pinned' => $pinnedComments,
                'regular' => $regularComments
            ];
        });
        
        $pinnedComments = $commentsData['pinned'];
        $regularComments = $commentsData['regular'];

        // Increment views (không cache)
        $ip = request()->ip();
        $sessionKey = "chapter_view_{$chapter->id}_{$ip}";
        if (!session()->has($sessionKey)) {
            $chapter->increment('views');
            session([$sessionKey => true]);
            session()->put($sessionKey, true, 1440);
        }

        return view('pages.chapter', array_merge($data, [
            'chapter' => $chapter,
            'recentReads' => $recentReads,
            'readingProgress' => $readingProgress,
            'pinnedComments' => $pinnedComments,
            'regularComments' => $regularComments,
            'hasAccess' => $hasAccess,
            'hasPasswordAccess' => $hasPasswordAccess,
            'hasPurchasedChapter' => $hasPurchasedChapter,
            'hasPurchasedStory' => $hasPurchasedStory
        ]));
    }

    /**
     * Load tất cả data cho trang chapter - tối ưu bằng batch loading
     */
    private function loadChapterData($storySlug, $chapterSlug)
    {
        $isNumber = is_numeric($chapterSlug);
        $isAdmin = Auth::check() && in_array(Auth::user()->role, ['admin_main', 'admin_sub']);

        // Load story với categories và user - chỉ select columns cần thiết
        $story = Story::where('slug', $storySlug)
            ->published()
            ->with([
                'categories' => function ($q) {
                    $q->select('categories.id', 'categories.name', 'categories.slug');
                },
                'user:id,name'
            ])
            ->select([
                'id', 'title', 'slug', 'cover', 'author_name', 
                'user_id', 'created_at', 'updated_at'
            ])
            ->firstOrFail();

        // Load chapter chính - chỉ select columns cần thiết
        if ($isNumber) {
            $chapterQuery = Chapter::where(function($q) use ($chapterSlug) {
                $q->where('number', $chapterSlug)
                  ->orWhere('id', $chapterSlug);
            })->where('story_id', $story->id);
        } else {
            $chapterQuery = Chapter::where('slug', $chapterSlug)
                ->where('story_id', $story->id);
        }

        if (!$isAdmin) {
            $chapterQuery->where('status', 'published');
        }

        $chapter = $chapterQuery
            ->select([
                'id', 'story_id', 'number', 'slug', 'title', 'content',
                'price', 'is_free', 'status', 'views',
                'created_at', 'updated_at'
            ])
            ->firstOrFail();

        // Redirect nếu là number nhưng slug khác
        if ($isNumber && $chapter->slug !== $chapterSlug) {
            return redirect()->route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug], 301);
        }

        // Tính word count
        $chapter->word_count = str_word_count(strip_tags($chapter->content), 0, 'àáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗộơớờởỡợùúũụủưứừửữựỳýỵỷỹ');

        // Load comments count và chapters trong một query
        $story->loadCount(['comments' => function ($q) {
            $q->whereNull('reply_id');
        }])
        ->load(['chapters' => function ($q) use ($isAdmin) {
            if (!$isAdmin) {
                $q->where('status', 'published');
            }
            $q->select('id', 'story_id', 'number', 'slug', 'title')
                ->orderBy('number', 'asc');
        }]);

        // Set comments count từ withCount
        $chapter->comments_count = $story->comments_count ?? 0;

        // Tìm next và prev chapter từ chapters đã load
        $allChapters = $story->chapters;
        $nextChapter = $allChapters->first(function ($ch) use ($chapter) {
            return $ch->number > $chapter->number;
        });

        $prevChapter = $allChapters->reverse()->first(function ($ch) use ($chapter) {
            return $ch->number < $chapter->number;
        });

        // Recent chapters (5 chapters gần nhất, loại trừ chapter hiện tại)
        $recentChapters = $allChapters
            ->where('id', '!=', $chapter->id)
            ->sortByDesc('number')
            ->take(5)
            ->values();

        return [
            'story' => $story,
            'chapter' => $chapter,
            'nextChapter' => $nextChapter,
            'prevChapter' => $prevChapter,
            'recentChapters' => $recentChapters,
        ];
    }

    public function checkChapterPassword(Request $request, $storySlug, $chapterSlug)
    {
        // Password feature không có trong database, trả về lỗi
        return response()->json([
            'success' => false,
            'message' => 'Tính năng mật khẩu chưa được kích hoạt.'
        ], 400);
    }

    public function searchChapters(Request $request)
    {
        $searchTerm = $request->search;
        $storyId = $request->story_id;

        $query = Chapter::query();

        // Filter by story ID when provided
        if ($storyId) {
            $query->where('story_id', $storyId);
        }

        // Visibility check
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin_main', 'admin_sub'])) {
            $query->where('status', 'published');
        }

        if ($searchTerm) {
            $searchNumber = preg_replace('/[^0-9]/', '', $searchTerm);

            $query->where(function ($q) use ($searchTerm, $searchNumber) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");

                if ($searchNumber !== '') {
                    $q->orWhere('number', $searchNumber);
                }
            });
        }

        $chapters = $query->orderBy('number', 'asc')->take(20)->get();

        return response()->json([
            'html' => view('components.search-results', compact('chapters'))->render()
        ]);
    }

    /**
     * Apply advanced search filters to the query
     */
    private function applyAdvancedFilters($query, Request $request, $skipQuery = false)
    {
        // Filter by search query (keywords) - skip if already applied
        if (!$skipQuery && $request->filled('query') && trim($request->input('query')) !== '') {
            $searchQuery = trim((string) $request->input('query'));
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('author_name', 'LIKE', "%{$searchQuery}%");
            });
        }

        // Filter by category
        if ($request->filled('category') && trim($request->input('category')) !== '') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Filter by completion status
        if ($request->filled('status') && trim($request->input('status')) !== '') {
            if ($request->status === 'completed') {
                $query->where('completed', true);
            } elseif ($request->status === 'ongoing') {
                $query->where('completed', false);
            }
        }

        // Filter by number of chapters
        if ($request->filled('chapters') && trim($request->input('chapters')) !== '') {
            $chaptersFilter = $request->chapters;

            // Use whereHas instead of withCount to avoid duplicate columns
            switch ($chaptersFilter) {
                case '1-10':
                    $query->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '>=', 1)
                    ->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '<=', 10);
                    break;
                case '11-50':
                    $query->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '>=', 11)
                    ->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '<=', 50);
                    break;
                case '51-100':
                    $query->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '>=', 51)
                    ->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '<=', 100);
                    break;
                case '100+':
                    $query->whereHas('chapters', function ($q) {
                        $q->where('status', 'published');
                    }, '>', 100);
                    break;
            }
        }

        // Apply sorting
        if ($request->filled('sort') && trim($request->input('sort')) !== '') {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'most_chapters':
                    // Check if chapters_count already exists
                    if (!$query->getQuery()->columns || !in_array('chapters_count', $query->getQuery()->columns)) {
                        $query->withCount(['chapters' => function ($q) {
                            $q->where('status', 'published');
                        }]);
                    }
                    $query->orderBy('chapters_count', 'desc');
                    break;
                case 'least_chapters':
                    // Check if chapters_count already exists
                    if (!$query->getQuery()->columns || !in_array('chapters_count', $query->getQuery()->columns)) {
                        $query->withCount(['chapters' => function ($q) {
                            $q->where('status', 'published');
                        }]);
                    }
                    $query->orderBy('chapters_count', 'asc');
                    break;
                case 'most_views':
                    // Check if chapters_count already exists
                    if (!$query->getQuery()->columns || !in_array('chapters_count', $query->getQuery()->columns)) {
                        $query->withCount(['chapters' => function ($q) {
                            $q->where('status', 'published');
                        }]);
                    }
                    $query->withSum('chapters', 'views')->orderBy('chapters_sum_views', 'desc');
                    break;
                case 'highest_rating':
                    $query->withAvg('ratings as average_rating', 'rating')->orderBy('average_rating', 'desc');
                    break;
            }
        }

        return $query;
    }
}

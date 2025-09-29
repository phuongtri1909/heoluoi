<?php

namespace App\Http\Controllers\Client;

use App\Models\Story;
use App\Models\Chapter;
use App\Models\Category;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
class SitemapController extends Controller
{
    public function index()
    {
        $sitemaps = [
            [
                'url' => route('sitemap.main'),
                'lastmod' => Carbon::now()->toAtomString()
            ],
            [
                'url' => route('sitemap.stories'),
                'lastmod' => Story::where('status', 'published')
                    ->latest('updated_at')
                    ->first()
                    ?->updated_at?->toAtomString() ?? Carbon::now()->toAtomString()
            ],
            [
                'url' => route('sitemap.chapters'),
                'lastmod' => Chapter::where('status', 'published')
                    ->latest('updated_at')
                    ->first()
                    ?->updated_at?->toAtomString() ?? Carbon::now()->toAtomString()
            ],
            [
                'url' => route('sitemap.categories'),
                'lastmod' => Category::latest('updated_at')
                    ->first()
                    ?->updated_at?->toAtomString() ?? Carbon::now()->toAtomString()
            ]
        ];

        return response()->view('sitemaps.index', [
            'sitemaps' => $sitemaps,
        ])->header('Content-Type', 'text/xml');
    }

    public function main()
    {
        $routes = [
            [
                'loc' => route('home'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => route('contact'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ],
            [
                'loc' => route('login'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.3'
            ],
            [
                'loc' => route('register'),
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.3'
            ]
        ];

        return response()->view('sitemaps.main', [
            'routes' => $routes,
        ])->header('Content-Type', 'text/xml');
    }

    public function stories()
    {
        $stories = Story::where('status', 'published')
            ->select('id', 'slug', 'updated_at')
            ->latest('updated_at')
            ->get();

        return response()->view('sitemaps.stories', [
            'stories' => $stories,
        ])->header('Content-Type', 'text/xml');
    }

    public function chapters()
    {
        $chapters = Chapter::where('status', 'published')
            ->select('id', 'story_id', 'slug', 'updated_at')
            ->with(['story:id,slug'])
            ->where('story_id', '!=', null)
            ->latest('updated_at')
            ->get();

        return response()->view('sitemaps.chapters', [
            'chapters' => $chapters,
        ])->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::select('id', 'slug', 'updated_at')
            ->get();

        return response()->view('sitemaps.categories', [
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}
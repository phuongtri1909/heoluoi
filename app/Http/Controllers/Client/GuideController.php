<?php

namespace App\Http\Controllers\Client;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class GuideController extends Controller
{
    /**
     * Display the guide page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return Guide::published()->latest()->first();

        return view('pages.guide.show', compact('guide'));
    }
} 
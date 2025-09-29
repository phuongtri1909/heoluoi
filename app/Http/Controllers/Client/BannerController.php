<?php

namespace App\Http\Controllers\Client;

use App\Models\Banner;
use App\Models\Story;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    public function click(Request $request, Banner $banner)
    {
        if ($banner->story_id) {
            $story = Story::find($banner->story_id);
            if ($story) {
                return redirect()->route('show.page.story', $story->slug);
            }
        }
        if (!empty($banner->link)) {
            return redirect()->away($banner->link);
        }
        return redirect()->route('home');
    }
}

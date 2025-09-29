<?php

namespace App\Http\Controllers\Client;

use App\Models\Story;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{

    public function storeClient(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'story_id' => 'required|exists:stories,id',
            'rating' => 'required|integer|min:1|max:5',
        ], [
            'rating.min' => 'Đánh giá phải từ 1 đến 5 sao.',
            'rating.max' => 'Đánh giá phải từ 1 đến 5 sao.',
            'story_id.exists' => 'Truyện không tồn tại.',
            'rating.required' => 'Vui lòng chọn số sao.',
            'story_id.required' => 'Truyện không tồn tại.'
        ]);
        
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đánh giá truyện.'
            ], 401);
        }
        
        $userId = Auth::id();
        $storyId = $validated['story_id'];
        $ratingValue = $validated['rating'];
        
        // Create or update the rating
        $rating = Rating::updateOrCreate(
            [
                'user_id' => $userId,
                'story_id' => $storyId
            ],
            [
                'rating' => $ratingValue
            ]
        );
        
        // Get updated average rating for the story
        $story = Story::find($storyId);
        $averageRating = $story->ratings()->avg('rating');
        $ratingsCount = $story->ratings()->count();
        
        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đã được ghi nhận!',
            'average' => round($averageRating, 1),
            'count' => $ratingsCount,
            'user_rating' => $ratingValue
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'story_id',
        'notification_enabled',
        'last_chapter_id',
        'last_read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'notification_enabled' => 'boolean',
        'last_read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the bookmark.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the story that is bookmarked.
     */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Get the last chapter that was read.
     */
    public function lastChapter()
    {
        return $this->belongsTo(Chapter::class, 'last_chapter_id');
    }

    /**
     * Toggle bookmark for a story.
     *
     * @param int $userId
     * @param int $storyId
     * @param int|null $chapterId
     * @return array
     */
    public static function toggleBookmark($userId, $storyId, $chapterId = null)
    {
        $bookmark = self::where('user_id', $userId)
            ->where('story_id', $storyId)
            ->first();
            
        if ($bookmark) {
            $bookmark->delete();
            return [
                'status' => 'removed',
                'message' => 'Đã xóa truyện khỏi danh sách theo dõi'
            ];
        } else {
            $data = [
                'user_id' => $userId,
                'story_id' => $storyId,
                'notification_enabled' => true,
            ];
            
            // Nếu có chapterId, lưu lại chương hiện tại
            if ($chapterId) {
                $data['last_chapter_id'] = $chapterId;
                $data['last_read_at'] = now();
            }
            
            self::create($data);
            
            return [
                'status' => 'added',
                'message' => 'Đã thêm truyện vào danh sách theo dõi'
            ];
        }
    }
    
    /**
     * Check if a user has bookmarked a story.
     *
     * @param int $userId
     * @param int $storyId
     * @return bool
     */
    public static function isBookmarked($userId, $storyId)
    {
        return self::where('user_id', $userId)
            ->where('story_id', $storyId)
            ->exists();
    }
    
    /**
     * Toggle notification setting for a bookmark.
     *
     * @param int $id
     * @return array
     */
    public static function toggleNotification($id)
    {
        $bookmark = self::findOrFail($id);
        $bookmark->notification_enabled = !$bookmark->notification_enabled;
        $bookmark->save();
        
        return [
            'status' => 'success',
            'notification_enabled' => $bookmark->notification_enabled,
            'message' => $bookmark->notification_enabled 
                ? 'Đã bật thông báo cho truyện này' 
                : 'Đã tắt thông báo cho truyện này'
        ];
    }
    
    /**
     * Update bookmark with current chapter.
     *
     * @param int $userId
     * @param int $storyId
     * @param int $chapterId
     * @return bool
     */
    public static function saveCurrentChapter($userId, $storyId, $chapterId)
    {
        $bookmark = self::where('user_id', $userId)
            ->where('story_id', $storyId)
            ->first();
            
        if ($bookmark) {
            $bookmark->last_chapter_id = $chapterId;
            $bookmark->last_read_at = now();
            return $bookmark->save();
        }
        
        return false;
    }
}
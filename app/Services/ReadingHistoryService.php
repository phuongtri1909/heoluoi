<?php

namespace App\Services;

use App\Models\Story;
use App\Models\Chapter;
use App\Models\UserReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ReadingHistoryService
{
    /**
     * Lưu tiến độ đọc của người dùng
     */
    public function saveReadingProgress(Story $story, Chapter $chapter, $progressPercent = 0)
    {
        if (Auth::check()) {
            // Người dùng đã đăng nhập, lưu vào database
            return $this->saveUserReadingProgress(Auth::id(), $story->id, $chapter->id, $progressPercent);
        } else {
            // Người dùng chưa đăng nhập, lưu vào session
            return $this->saveSessionReadingProgress($story->id, $chapter->id, $progressPercent);
        }
    }
    
    /**
     * Lưu tiến độ đọc vào database cho người dùng đã đăng nhập
     */
    private function saveUserReadingProgress($userId, $storyId, $chapterId, $progressPercent)
    {
        return UserReading::updateOrCreate(
            [
                'user_id' => $userId,
                'story_id' => $storyId
            ],
            [
                'chapter_id' => $chapterId,
                'progress_percent' => $progressPercent,
                'updated_at' => now()
            ]
        );
    }
    
    /**
     * Lưu tiến độ đọc vào session cho người dùng chưa đăng nhập
     */
    private function saveSessionReadingProgress($storyId, $chapterId, $progressPercent)
    {
        // Lấy hoặc tạo key đại diện cho thiết bị người dùng
        $deviceKey = $this->getOrCreateDeviceKey();
        
        // Lưu vào database với device_key
        return UserReading::updateOrCreate(
            [
                'session_id' => $deviceKey,
                'story_id' => $storyId,
                'user_id' => null
            ],
            [
                'chapter_id' => $chapterId,
                'progress_percent' => $progressPercent,
                'updated_at' => now()
            ]
        );
    }
    
    /**
     * Lấy hoặc tạo key đại diện cho thiết bị người dùng
     * Key này được lưu trong cookie và ổn định ngay cả khi đăng nhập/đăng xuất
     */
    public function getOrCreateDeviceKey()
    {
        $cookieName = 'reader_device_key';
        
        // Kiểm tra xem đã có cookie chứa device key chưa
        if (!Cookie::has($cookieName) && !request()->cookie($cookieName)) {
            // Tạo một device key mới
            $deviceKey = 'device_' . Str::uuid()->toString();
            
            // Lưu device key vào cookie, thời hạn 1 năm
            Cookie::queue($cookieName, $deviceKey, 525600);
            
            // Cũng lưu vào session để sử dụng ngay trong request hiện tại
            Session::put($cookieName, $deviceKey);
            
            return $deviceKey;
        }
        
        // Lấy từ cookie hoặc từ session nếu cookie chưa có sẵn trong request hiện tại
        $deviceKey = request()->cookie($cookieName) ?? Session::get($cookieName);
        
        // Lưu vào session để đảm bảo có thể sử dụng trong request hiện tại
        Session::put($cookieName, $deviceKey);
        
        return $deviceKey;
    }
    
    /**
     * Lấy danh sách 5 truyện đọc gần đây
     */
    public function getRecentReadings($limit = 5)
    {
        if (Auth::check()) {
            // Người dùng đã đăng nhập
            return $this->getUserRecentReadings(Auth::id(), $limit);
        } else {
            // Người dùng chưa đăng nhập
            return $this->getSessionRecentReadings($limit);
        }
    }
    
    /**
     * Lấy truyện đọc gần đây của người dùng đã đăng nhập
     */
    private function getUserRecentReadings($userId, $limit)
    {
        return UserReading::with(['story', 'chapter'])
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->take($limit)
            ->get();
    }
    
    /**
     * Lấy truyện đọc gần đây từ session
     */
    private function getSessionRecentReadings($limit)
    {
        $deviceKey = $this->getOrCreateDeviceKey();
        
        return UserReading::with(['story', 'chapter'])
            ->where('session_id', $deviceKey)
            ->whereNull('user_id')
            ->orderByDesc('updated_at')
            ->take($limit)
            ->get();
    }
    
    /**
     * Chuyển dữ liệu đọc từ session sang user khi đăng nhập
     */
    public function migrateSessionReadingsToUser($userId)
    {
        // Sử dụng device key thay vì session key
        $deviceKey = $this->getOrCreateDeviceKey();
        
        $sessionReadings = UserReading::where('session_id', $deviceKey)
            ->whereNull('user_id')
            ->get();
        
        foreach ($sessionReadings as $reading) {
            // Kiểm tra xem người dùng đã có bản ghi cho truyện này chưa
            $userReading = UserReading::where('user_id', $userId)
                ->where('story_id', $reading->story_id)
                ->first();
                
            // Nếu người dùng chưa đọc truyện này hoặc đọc truyện này nhưng cũ hơn
            if (!$userReading || $reading->updated_at > $userReading->updated_at) {
                UserReading::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'story_id' => $reading->story_id
                    ],
                    [
                        'chapter_id' => $reading->chapter_id,
                        'progress_percent' => $reading->progress_percent,
                        'updated_at' => $reading->updated_at
                    ]
                );
            }
            
            // Xóa bản ghi session sau khi đã chuyển dữ liệu
            $reading->delete();
        }
    }
    
    /**
     * Sao chép dữ liệu đọc của người dùng sang session khi đăng xuất
     *
     * @param int $userId ID của người dùng đang đăng xuất
     * @return void
     */
    public function copyUserReadingsToSession($userId)
    {
        // Sử dụng device key thay vì session key
        $deviceKey = $this->getOrCreateDeviceKey();
        
        // Lấy các bản ghi đọc của người dùng
        $userReadings = UserReading::where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->take(10)  // Tăng lên 10 bản ghi để lưu nhiều truyện hơn
            ->get();
        
        // Xóa các bản ghi session cũ (nếu có)
        UserReading::where('session_id', $deviceKey)
            ->whereNull('user_id')
            ->delete();
        
        // Sao chép các bản ghi từ user vào session
        foreach ($userReadings as $reading) {
            UserReading::create([
                'session_id' => $deviceKey,
                'user_id' => null,
                'story_id' => $reading->story_id,
                'chapter_id' => $reading->chapter_id,
                'progress_percent' => $reading->progress_percent,
                'updated_at' => $reading->updated_at,
                'created_at' => now(),
            ]);
        }
    }
    
    /**
     * Xóa các bản ghi session cũ
     * Nên chạy định kỳ để dọn dẹp DB
     *
     * @param int $days Số ngày để coi là cũ
     * @return int Số bản ghi đã xóa
     */
    public function cleanupOldSessionReadings($days = 30)
    {
        $cutoffDate = now()->subDays($days);
        
        return UserReading::whereNotNull('session_id')
            ->whereNull('user_id')
            ->where('updated_at', '<', $cutoffDate)
            ->delete();
    }
}
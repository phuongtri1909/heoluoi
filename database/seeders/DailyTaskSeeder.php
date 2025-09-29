<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\DailyTask;
use Illuminate\Database\Seeder;

class DailyTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating default daily tasks...');
        
        $defaultTasks = [
            [
                'name' => 'Đăng nhập hàng ngày',
                'type' => DailyTask::TYPE_LOGIN,
                'description' => 'Đăng nhập vào trang web để nhận thưởng',
                'max_per_day' => 1,
                'order' => 1,
            ],
            [
                'name' => 'Bình luận truyện',
                'type' => DailyTask::TYPE_COMMENT,
                'description' => 'Viết bình luận cho truyện để nhận thưởng',
                'max_per_day' => 1,
                'order' => 2,
            ],
            [
                'name' => 'Theo dõi truyện',
                'type' => DailyTask::TYPE_BOOKMARK,
                'description' => 'Theo dõi truyện mới để nhận thưởng',
                'max_per_day' => 1,
                'order' => 3,
            ],
            [
                'name' => 'Chia sẻ truyện',
                'type' => DailyTask::TYPE_SHARE,
                'description' => 'Chia sẻ truyện lên mạng xã hội để nhận thưởng',
                'max_per_day' => 1,
                'order' => 4,
            ],
        ];

        foreach ($defaultTasks as $task) {
            if (!DailyTask::where('type', $task['type'])->exists()) {
                DailyTask::create($task);
            }
        }
        
        $this->command->info('Default daily tasks created successfully!');
    }
}
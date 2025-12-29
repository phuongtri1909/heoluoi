<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Config;

class RateLimitConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::setConfig(
            'rate_limit_max_pages',
            7,
            'Số trang tối đa có thể chuyển trong khoảng thời gian ngắn (rate limit)'
        );

        Config::setConfig(
            'rate_limit_time_window',
            30,
            'Khoảng thời gian (giây) để đếm số lần chuyển trang cho rate limit'
        );

        Config::setConfig(
            'rate_limit_ban_threshold',
            3,
            'Số lần vi phạm rate limit trong 1 ngày trước khi khóa tài khoản'
        );

        Config::setConfig(
            'rate_limit_enabled',
            1,
            'Bật/tắt rate limit (1=bật, 0=tắt)'
        );

        Config::setConfig(
            'rate_limit_delay_seconds',
            5,
            'Số giây delay response khi vi phạm rate limit (giai đoạn 1 - delay)'
        );

        Config::setConfig(
            'rate_limit_delay_cooldown_minutes',
            1,
            'Số phút cooldown sau delay, trong thời gian này nếu vi phạm tiếp thì vẫn bị xử lý (sau khi hết cooldown thì reset về bình thường)'
        );

        Config::setConfig(
            'rate_limit_temp_ban_minutes',
            30,
            'Số phút chặn tạm thời khi vi phạm rate limit (giai đoạn 2 - temp ban)'
        );
    }
}

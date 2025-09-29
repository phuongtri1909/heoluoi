<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Config::setConfig(
            'coin_bank_percentage',
            15,
            'Phí chuyển khoản ngân hàng (15%)'
        );

        Config::setConfig(
            'coin_exchange_rate',
            100,
            'quy đổi tiền sang xu 100 VND = 1 xu'
        );

        Config::setConfig(
            'coin_bank_auto_percentage',
            0,
            'Phí nạp ngân hàng (0%) nếu có nhập thì mới tính'
        );

        Config::setConfig(
            'coin_card_percentage',
            30,
            'Phí nạp thẻ (%)'
        );

        Config::setConfig(
            'card_wrong_amount_penalty',
            50,
            'Số tiền phạt nếu người dùng nhập sai số tiền rút (50% = trừ 50% giá trị thẻ thực)'
        );

        Config::setConfig(
            'daily_task_login_reward',
            1,
            'Số xu thưởng khi hoàn thành nhiệm vụ đăng nhập hàng ngày'
        );

        Config::setConfig(
            'daily_task_comment_reward',
            1,
            'Số xu thưởng khi hoàn thành nhiệm vụ bình luận truyện'
        );

        Config::setConfig(
            'daily_task_bookmark_reward',
            1,
            'Số xu thưởng khi hoàn thành nhiệm vụ theo dõi truyện'
        );

        Config::setConfig(
            'daily_task_share_reward',
            1,
            'Số xu thưởng khi hoàn thành nhiệm vụ chia sẻ truyện'
        );
    }
} 
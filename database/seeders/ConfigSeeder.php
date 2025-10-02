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
            'coin_exchange_rate',
            10,
            'quy đổi tiền sang cám 100 VND = 1 cám'
        );

        Config::setConfig('bonus_base_amount', 100000, 'Mốc số tiền đầu tiên để tính thưởng');
        Config::setConfig('bonus_base_cam',    300,    'Cám tặng tại mốc base_amount');

        Config::setConfig('bonus_double_amount', 200000, 'Mốc số tiền thứ 2');
        Config::setConfig('bonus_double_cam',    1000,   'Cám tặng tại mốc double_amount');

        Config::setConfig(
            'coin_bank_percentage',
            0,
            'Phí chuyển khoản ngân hàng thủ công (15%)'
        );

        Config::setConfig(
            'coin_bank_auto_percentage',
            0,
            'Phí nạp ngân hàng tự động (0%) nếu có nhập thì mới tính'
        );

        Config::setConfig(
            'coin_paypal_rate',
            20000,
            'quy đổi 1 đô sang bao nhiêu tiền việt'
        );

        Config::setConfig(
            'coin_paypal_percentage',
            0,
            'Phí nạp paypal (0%) nếu có nhập thì mới tính'
        );

        Config::setConfig(
            'coin_card_percentage',
            20,
            'Phí nạp thẻ (%)'
        );

        Config::setConfig(
            'card_wrong_amount_penalty',
            50,
            'Số tiền phạt nếu người dùng nhập sai số tiền rút (50% = trừ 50% giá trị thẻ thực)'
        );

        Config::setConfig(
            'daily_task_login_reward',
            10,
            'Số cám thưởng khi hoàn thành nhiệm vụ đăng nhập hàng ngày'
        );

        Config::setConfig(
            'daily_task_comment_reward',
            10,
            'Số cám thưởng khi hoàn thành nhiệm vụ bình luận truyện'
        );

        Config::setConfig(
            'daily_task_bookmark_reward',
            10,
            'Số cám thưởng khi hoàn thành nhiệm vụ theo dõi truyện'
        );
    }
}

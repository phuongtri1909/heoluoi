<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class EnableRegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::setConfig(
            'enable_register',
            0,
            'Bật/tắt đăng ký tài khoản mới (1=bật, 0=đóng đăng ký). Login bằng mật khẩu luôn hoạt động cho tài khoản đã có.'
        );
    }
}

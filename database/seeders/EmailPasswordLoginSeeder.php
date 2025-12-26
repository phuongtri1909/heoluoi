<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class EmailPasswordLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::setConfig(
            'enable_email_password_login',
            0,
            'Bật/tắt đăng nhập bằng email mật khẩu (1=bật, 0=tắt)'
        );
    }
}


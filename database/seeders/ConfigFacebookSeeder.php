<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigFacebookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Config::setConfig(
            'facebook_page_url',
            'https://www.facebook.com/profile.php?id=61572454674711',
            'URL fan page Facebook (dùng cho footer, trang chương, trang nạp cám)'
        );
    }
}

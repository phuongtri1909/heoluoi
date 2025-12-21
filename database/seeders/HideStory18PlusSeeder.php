<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class HideStory18PlusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::setConfig(
            'hide_story_18_plus',
            0,
            'Ẩn truyện 18+ ở trang chủ (1=ẩn, 0=hiện)'
        );
    }
}



<?php

namespace App\Services;

use App\Models\Config;

class ConfigService
{
    /**
     * Get hide_story_18_plus config (helper method)
     */
    public function shouldHide18Plus(): bool
    {
        return (int) Config::getConfig('hide_story_18_plus', 0) === 1;
    }
}


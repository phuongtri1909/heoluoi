<?php

namespace App\Observers;

use App\Models\Config;
use Illuminate\Support\Facades\Cache;

class ConfigObserver
{
    /**
     * Handle the Config "saved" event.
     * Clear cache when config is created or updated
     */
    public function saved(Config $config): void
    {
        $this->clearConfigCache($config->key);
    }

    /**
     * Handle the Config "deleted" event.
     */
    public function deleted(Config $config): void
    {
        $this->clearConfigCache($config->key);
    }

    /**
     * Clear cache for specific config key
     * Clear cả Laravel Cache và request cache
     */
    private function clearConfigCache(string $key): void
    {
        // Clear Laravel cache
        Cache::forget("config.{$key}");
        
        // Clear request cache
        Config::clearRequestCache($key);
    }
}


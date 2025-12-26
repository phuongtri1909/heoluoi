<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Config extends Model
{
    use HasFactory;

    private static array $requestCache = [];

    protected $fillable = [
        'key',
        'value',
        'description'
    ];

    /**
     * Get a configuration value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getConfig($key, $default = null)
    {
        if (isset(self::$requestCache[$key])) {
            return self::$requestCache[$key];
        }

        $cacheKey = "config.{$key}";

        $value = Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $config = self::where('key', $key)->first();
            return $config ? $config->value : $default;
        });

        self::$requestCache[$key] = $value;

        return $value;
    }

    /**
     * Set a configuration value
     * 
     * @param string $key
     * @param mixed $value
     * @param string $description
     * @return Config
     */
    public static function setConfig($key, $value, $description = null)
    {
        $config = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description
            ]
        );

        $cacheKey = "config.{$key}";
        Cache::forget($cacheKey);
        
        self::clearRequestCache($key);

        return $config;
    }

    public static function getConfigs(array $defaults = [])
    {
        $keys = array_keys($defaults);

        $values = self::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        return array_merge($defaults, $values);
    }

    /**
     * Clear request cache for a specific key
     * 
     * @param string $key
     * @return void
     */
    public static function clearRequestCache(string $key): void
    {
        if (isset(self::$requestCache[$key])) {
            unset(self::$requestCache[$key]);
        }
    }

    /**
     * Clear all request cache
     * 
     * @return void
     */
    public static function clearAllRequestCache(): void
    {
        self::$requestCache = [];
    }
}

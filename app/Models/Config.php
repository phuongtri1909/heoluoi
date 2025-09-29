<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

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
        $config = self::where('key', $key)->first();
        return $config ? $config->value : $default;
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
}

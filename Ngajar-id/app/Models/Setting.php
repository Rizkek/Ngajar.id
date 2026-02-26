<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return self::all()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value, $group = 'general')
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('app_settings');
    }

    /**
     * Get all settings grouped
     */
    public static function getAllGrouped()
    {
        $all = self::all();
        $grouped = [];
        foreach ($all as $setting) {
            $grouped[$setting->group][$setting->key] = $setting->value;
        }
        return $grouped;
    }
}

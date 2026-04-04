<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with caching.
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever("system_setting_{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("system_setting_{$key}");
        Cache::forget("all_system_settings");
        return $setting;
    }

    /**
     * Get all settings as a key-value collection with caching.
     */
    public static function allCached()
    {
        $settings = Cache::rememberForever("all_system_settings", function () {
            return self::all()->pluck('value', 'key');
        });

        // Robustness: If cache is corrupted (e.g., __PHP_Incomplete_Class), clear and refetch
        if (!($settings instanceof \Illuminate\Support\Collection)) {
            Cache::forget("all_system_settings");
            return self::all()->pluck('value', 'key');
        }

        return $settings;
    }

    /**
     * Get the system currency symbol.
     */
    public static function getCurrencySymbol()
    {
        $settings = self::allCached();
        return $settings['system_currency_symbol'] ?? $settings['system_currency'] ?? 'EGP';
    }
}

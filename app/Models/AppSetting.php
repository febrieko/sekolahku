<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /**
     * Get a setting by key with a default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("app_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting && $setting->value !== null ? $setting->value : $default;
        });
    }

    /**
     * Set a setting by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("app_setting_{$key}");
        Cache::forget('all_app_branding_settings');
    }

    /**
     * Get all branding settings as an associative array.
     */
    public static function getBranding(): array
    {
        return Cache::rememberForever('all_app_branding_settings', function () {
            try {
                $settings = static::where('group', 'branding')->pluck('value', 'key')->toArray();
            } catch (\Throwable) {
                $settings = [];
            }

            return array_merge([
                'app_name' => 'Sekolahku',
                'app_tagline' => 'v2.0',
                'app_logo' => null,
            ], $settings);
        });
    }

    /**
     * Clear all cached settings.
     */
    public static function clearCache(): void
    {
        Cache::forget('all_app_branding_settings');
        Cache::forget('app_setting_app_name');
        Cache::forget('app_setting_app_tagline');
        Cache::forget('app_setting_app_logo');
    }
}


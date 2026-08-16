<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $settings = Cache::remember('store_settings', 3600, function () {
            return self::pluck('value', 'key')->all();
        });

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function set(string $key, $value): self
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('store_settings');
        });
        static::deleted(function () {
            Cache::forget('store_settings');
        });
    }
}
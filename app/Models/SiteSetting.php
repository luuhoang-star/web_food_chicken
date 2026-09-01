<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Tự động xóa cache khi có bất kỳ cấu hình nào thay đổi.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('site_settings_all_keyed');
        });

        static::deleted(function () {
            Cache::forget('site_settings_all_keyed');
        });
    }

    /**
     * Get a setting value by key with fallback (Lấy siêu tốc từ Cache).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allKeyed();

        return (array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '')
            ? $all[$key]
            : $default;
    }

    /**
     * Get all settings as key-value array with permanent caching.
     *
     * @return array<string, mixed>
     */
    public static function allKeyed(): array
    {
        return Cache::rememberForever('site_settings_all_keyed', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}

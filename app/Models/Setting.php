<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }

        return Cache::rememberForever(self::cacheKey($key), function () use ($key, $default) {
            $row = self::query()->where('key', $key)->first();
            return $row?->value ?? $default;
        });
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? '1' : '0');

        if (is_bool($value)) {
            return $value;
        }

        $value = is_string($value) ? strtolower(trim($value)) : $value;

        return in_array($value, [1, '1', 'true', 'yes', 'on'], true);
    }

    public static function set(string $key, string|int|bool|null $value): void
    {
        if (!Schema::hasTable('settings')) {
            throw new \RuntimeException("Missing 'settings' table. Run 'php artisan migrate' first.");
        }

        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : ($value === null ? null : (string) $value)]
        );

        Cache::forget(self::cacheKey($key));
    }

    private static function cacheKey(string $key): string
    {
        return 'setting:' . $key;
    }
}

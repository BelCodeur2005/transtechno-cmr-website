<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /** Lit une valeur depuis le cache (TTL 1h). */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, fn () =>
            static::where('key', $key)->value('value')
        ) ?? $default;
    }

    /** Écrit une valeur et invalide le cache de cette clé. */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("setting:{$key}");
    }

    /** Invalide tout le cache des settings (après sauvegarde groupée). */
    public static function flushCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting:{$key}");
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'label'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 300, function () use ($key, $default) {
            try {
                $row = static::where('key', $key)->first();
                return $row ? $row->value : $default;
            } catch (\Throwable) {
                return $default;
            }
        });
    }

    public static function setValue(string $key, mixed $value, string $group = 'general', ?string $label = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'group' => $group,
                'label' => $label,
            ], fn ($v) => $v !== null)
        );
        Cache::forget("setting_{$key}");
    }
}

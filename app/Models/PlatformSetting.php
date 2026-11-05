<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Retrieve a platform setting with a fallback value.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()
            ->where('key', $key)
            ->value('value');

        return $setting ?? $default;
    }

    /**
     * Create or update a platform setting.
     */
    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value)
                ? json_encode($value, JSON_THROW_ON_ERROR)
                : (string) $value]
        );
    }
}

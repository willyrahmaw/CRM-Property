<?php

namespace App\Support\Traits;

use Illuminate\Support\Str;

/**
 * Trait for models with UUID keys.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static void creating(\Closure $callback)
 */
trait HasUuid
{
    /**
     * Boot the trait and register model event hooks.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $keyName = $model->getKeyName();
            if (empty($model->{$keyName})) {
                $model->{$keyName} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}

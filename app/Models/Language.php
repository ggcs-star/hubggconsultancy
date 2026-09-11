<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Language extends Model
{
    use HasFactory;

    /**
     * Sentinel value the shared language-select component submits when an
     * admin picks "+ Add Language" instead of an existing option.
     */
    public const NEW_LANGUAGE_VALUE = '__new__';

    protected $fillable = [
        'code',
        'name',
        'sort_order',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Turns whatever a language <select> submitted into a real language
     * code — creating a new Language row on the fly when the admin picked
     * "+ Add Language", so it's immediately available everywhere else this
     * same dropdown appears. Shared by every content type's store()/update().
     */
    public static function resolveCode(string $language, ?string $newName): string
    {
        if ($language !== self::NEW_LANGUAGE_VALUE) {
            return $language;
        }

        $name = trim((string) $newName);

        if ($name === '') {
            throw ValidationException::withMessages([
                'new_language_name' => 'Enter a name for the new language.',
            ]);
        }

        $code = Str::slug($name, '_');

        $language = static::firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'sort_order' => (static::max('sort_order') ?? 0) + 1]
        );

        return $language->code;
    }
}

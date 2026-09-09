<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'hindi_thumbnail',
        'english_thumbnail',
        'gujarati_thumbnail',
        'marathi_thumbnail',
        'telugu_thumbnail',
        'kannada_thumbnail',
        'hindi_youtube_url',
        'english_youtube_url',
        'gujarati_youtube_url',
        'marathi_youtube_url',
        'telugu_youtube_url',
        'kannada_youtube_url',
        'is_published',
        'sort_order',
    ];

    /**
     * Every language this feature supports, in a stable display order —
     * shared by the admin form/checkpoint tabs and the user-facing language
     * switcher so there's one place to add a language in the future.
     */
    public const LANGUAGES = ['english', 'hindi', 'gujarati', 'marathi', 'telugu', 'kannada'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        // This app's tables are MyISAM (no FK support), so ON DELETE CASCADE in the
        // migrations is not actually enforced by the database — cascade manually.
        // Answers are deliberately never touched here — see ResourceCheckpoint.
        static::deleting(function (self $resource) {
            $resource->checkpoints->each->delete();
        });
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(ResourceCheckpoint::class);
    }

    public function checkpointsFor(string $language): HasMany
    {
        return $this->checkpoints()->where('language', $language)->orderBy('sort_order');
    }

    // Falls back to the other languages' thumbnails, then the legacy shared thumbnail
    // (from before per-language thumbnails existed), when one hasn't been uploaded.
    public function thumbnailFor(string $language): ?string
    {
        $ordered = collect(self::LANGUAGES)->prepend($language)->unique();

        foreach ($ordered as $candidate) {
            if ($value = $this->{"{$candidate}_thumbnail"} ?? null) {
                return $value;
            }
        }

        return $this->thumbnail;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}

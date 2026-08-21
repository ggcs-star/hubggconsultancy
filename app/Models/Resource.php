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
        'hindi_youtube_url',
        'english_youtube_url',
        'is_published',
        'sort_order',
    ];

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

    // Falls back to the other language's thumbnail, then the legacy shared thumbnail
    // (from before per-language thumbnails existed), when one hasn't been uploaded.
    public function thumbnailFor(string $language): ?string
    {
        return match ($language) {
            'hindi' => $this->hindi_thumbnail ?: $this->english_thumbnail ?: $this->thumbnail,
            'english' => $this->english_thumbnail ?: $this->hindi_thumbnail ?: $this->thumbnail,
            default => $this->thumbnail,
        };
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}

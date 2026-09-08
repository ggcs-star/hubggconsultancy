<?php

namespace App\Models;

use App\Traits\HasGoogleDriveLink;
use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, HasGoogleDriveLink, HasSortOrder;

    protected $fillable = [
        'title',
        'description',
        'language',
        'thumbnail',
        'url',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        $fileId = $this->extractDriveFileId($this->url);

        return $fileId ? "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000" : null;
    }

    /**
     * Drive's own in-page preview URL, embeddable in an iframe so the
     * document opens inside the app instead of a new browser tab. Null for
     * non-Drive links, which get opened in a new tab as a fallback since we
     * can't know whether the target host allows framing.
     */
    public function embedUrl(): ?string
    {
        $fileId = $this->extractDriveFileId($this->url);

        return $fileId ? "https://drive.google.com/file/d/{$fileId}/preview" : null;
    }
}

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
     * non-Drive links (e.g. a short link that redirects to Drive), where we
     * can't rewrite a file ID out of the URL itself.
     */
    public function embedUrl(): ?string
    {
        $fileId = $this->extractDriveFileId($this->url);

        return $fileId ? "https://drive.google.com/file/d/{$fileId}/preview" : null;
    }

    /**
     * What actually goes in the preview modal's iframe: Drive's own preview
     * URL when we can tell it's a Drive link, otherwise the stored URL
     * as-is (e.g. a tracklio.in short link that redirects to the real
     * file) — every document opens inside the app; "Open in new tab" in the
     * modal is the fallback for the rare host that refuses to be framed.
     */
    public function previewUrl(): string
    {
        return $this->embedUrl() ?? $this->url;
    }
}

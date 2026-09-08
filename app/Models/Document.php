<?php

namespace App\Models;

use App\Traits\HasGoogleDriveLink;
use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, HasGoogleDriveLink, HasSortOrder;

    protected $fillable = [
        'title',
        'description',
        'language',
        'thumbnail',
        'url',
        'is_external',
        'original_filename',
        'mime_type',
        'file_size',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_external' => 'boolean',
        'file_size' => 'integer',
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

        if (! $this->is_external) {
            return null;
        }

        $fileId = $this->extractDriveFileId($this->url);

        return $fileId ? "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000" : null;
    }

    /**
     * The real, browsable URL regardless of how this document was added —
     * the external link as-is, or the public storage URL for an uploaded
     * file. This (not the raw `url` column) is what "Open in new tab"
     * should always use, since an uploaded file's `url` is just a path on
     * the "public" disk, not a full URL.
     */
    public function fileUrl(): string
    {
        return $this->is_external ? $this->url : Storage::disk('public')->url($this->url);
    }

    /**
     * Drive's own in-page preview URL, embeddable in an iframe so the
     * document opens inside the app instead of a new browser tab. Null for
     * non-Drive links (e.g. a short link that redirects to Drive), where we
     * can't rewrite a file ID out of the URL itself.
     */
    public function embedUrl(): ?string
    {
        if (! $this->is_external) {
            return null;
        }

        $fileId = $this->extractDriveFileId($this->url);

        return $fileId ? "https://drive.google.com/file/d/{$fileId}/preview" : null;
    }

    /**
     * What actually goes in the preview modal's iframe: Drive's own preview
     * URL when we can tell it's a Drive link, the stored external URL
     * as-is (e.g. a tracklio.in short link that redirects to the real
     * file), or the uploaded file's own URL — every document opens inside
     * the app; "Open in new tab" in the modal is the fallback for the rare
     * host/format that refuses to render inside an iframe.
     */
    public function previewUrl(): string
    {
        return $this->is_external ? ($this->embedUrl() ?? $this->url) : $this->fileUrl();
    }
}

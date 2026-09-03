<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, HasSortOrder;

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

        $fileId = $this->driveFileId();

        return $fileId ? "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000" : null;
    }

    /**
     * Extract the file ID from a Google Drive/Docs/Sheets/Slides URL so we
     * can fall back to Drive's own thumbnail when no thumbnail was uploaded.
     * Only works if the file is shared as "Anyone with the link".
     */
    private function driveFileId(): ?string
    {
        if (preg_match('#/d/([a-zA-Z0-9_-]{10,})#', $this->url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/[?&]id=([a-zA-Z0-9_-]{10,})/', $this->url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

<?php

namespace App\Models;

use App\Traits\HasGoogleDriveLink;
use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SalesToolkitItem extends Model
{
    use HasFactory, HasGoogleDriveLink, HasSortOrder;

    protected $fillable = [
        'title',
        'category',
        'description',
        'language',
        'thumbnail',
        'url',
        'is_drive_link',
        'original_filename',
        'mime_type',
        'file_size',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_drive_link' => 'boolean',
        'file_size' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function fileUrl(): string
    {
        return $this->is_drive_link ? $this->url : Storage::disk('public')->url($this->url);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /**
     * Drive's own in-page preview URL, embeddable in an iframe so the item
     * opens inside the app instead of a new browser tab — same treatment as
     * Documents. Null for uploaded files, which keep using the custom
     * PDF/Office reader instead.
     */
    public function embedUrl(): ?string
    {
        if (! $this->is_drive_link) {
            return null;
        }

        $fileId = $this->extractDriveFileId($this->url);

        return $fileId ? "https://drive.google.com/file/d/{$fileId}/preview" : null;
    }
}

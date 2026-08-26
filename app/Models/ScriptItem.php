<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ScriptItem extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'script_topic_id',
        'type',
        'title',
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

    public function sortOrderScopeColumn(): ?string
    {
        return 'script_topic_id';
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ScriptTopic::class, 'script_topic_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('type', 'video');
    }

    public function scopeDocuments(Builder $query): Builder
    {
        return $query->where('type', 'document');
    }

    public function fileUrl(): string
    {
        return $this->is_external ? $this->url : Storage::disk('public')->url($this->url);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }
}

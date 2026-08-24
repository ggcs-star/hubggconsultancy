<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SalesToolkitItem extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'title',
        'category',
        'description',
        'url',
        'original_filename',
        'mime_type',
        'file_size',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'file_size' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function fileUrl(): string
    {
        return Storage::disk('public')->url($this->url);
    }
}

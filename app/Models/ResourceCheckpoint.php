<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceCheckpoint extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'resource_id',
        'language',
        'timestamp_seconds',
        'title',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $checkpoint) {
            $checkpoint->questions->each->delete();
        });
    }

    public function sortOrderScopeColumn(): string
    {
        return 'resource_id';
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ResourceQuizQuestion::class)->orderBy('sort_order');
    }
}

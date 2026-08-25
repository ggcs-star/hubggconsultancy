<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScriptTopic extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'title',
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
        static::deleting(function (self $topic) {
            $topic->items->each->delete();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(ScriptItem::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}

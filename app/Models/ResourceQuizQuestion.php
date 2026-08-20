<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceQuizQuestion extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'resource_checkpoint_id',
        'type',
        'question_text',
        'points',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $question) {
            $question->options()->delete();
        });
    }

    public function sortOrderScopeColumn(): string
    {
        return 'resource_checkpoint_id';
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(ResourceCheckpoint::class, 'resource_checkpoint_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ResourceQuizOption::class)->orderBy('sort_order');
    }
}

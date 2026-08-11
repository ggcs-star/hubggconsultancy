<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseQuizQuestion extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'quiz_checkpoint_id',
        'type',
        'question_text',
        'points',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'quiz_checkpoint_id';
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(CourseQuizCheckpoint::class, 'quiz_checkpoint_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(CourseQuizOption::class, 'quiz_question_id')->orderBy('sort_order');
    }
}

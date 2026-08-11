<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseQuizOption extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'quiz_question_id',
        'option_text',
        'is_correct',
        'sort_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'quiz_question_id';
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(CourseQuizQuestion::class, 'quiz_question_id');
    }
}

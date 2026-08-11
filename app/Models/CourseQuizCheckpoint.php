<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseQuizCheckpoint extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'course_id',
        'course_module_id',
        'course_lesson_id',
        'after_course_lesson_id',
        'timestamp_seconds',
        'title',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'course_module_id';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CourseQuizQuestion::class, 'quiz_checkpoint_id')->orderBy('sort_order');
    }

    public function isModuleQuiz(): bool
    {
        return is_null($this->course_lesson_id);
    }
}

<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class CourseModule extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'course_id';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)->orderBy('sort_order');
    }

    public function moduleQuizzes(): HasMany
    {
        return $this->hasMany(CourseQuizCheckpoint::class)->whereNull('course_lesson_id')->orderBy('sort_order');
    }

    /**
     * Lessons + module-level quizzes merged into one admin-facing,
     * drag-orderable sequence for this module.
     */
    public function orderedItems()
    {
        $lessons = $this->lessons->each(fn ($lesson) => $lesson->item_type = 'lesson');
        $quizzes = $this->moduleQuizzes->each(fn ($quiz) => $quiz->item_type = 'module_quiz');

        return $lessons->concat($quizzes)->sortBy('sort_order')->values();
    }

    /**
     * Same merged sequence, decorated with the current user's completion
     * state for the client-facing course player sidebar.
     */
    public function getItemsForClientAttribute()
    {
        $userId = Auth::id();

        $completedLessonIds = CourseLessonProgress::where('user_id', $userId)
            ->where('completed', true)
            ->whereIn('course_lesson_id', $this->lessons->pluck('id'))
            ->pluck('course_lesson_id');

        $lessons = $this->lessons->map(function ($lesson) use ($completedLessonIds) {
            $lesson->item_type = 'lesson';
            $lesson->client_completed = $completedLessonIds->contains($lesson->id);

            return $lesson;
        });

        $quizzes = $this->moduleQuizzes->map(function ($quiz) use ($userId) {
            $quiz->item_type = 'module_quiz';
            $questionIds = $quiz->questions->pluck('id');
            $answeredCount = QuizAnswer::where('user_id', $userId)->whereIn('quiz_question_id', $questionIds)->count();
            $quiz->client_completed = $questionIds->isNotEmpty() && $answeredCount >= $questionIds->count();

            return $quiz;
        });

        return $lessons->concat($quizzes)->sortBy('sort_order')->values();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'certificate_background',
        'certificate_fields',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'certificate_fields' => 'array',
    ];

    /**
     * Sensible layout if the admin uploads a background but never touches
     * the position/color controls — center-anchored (each "left" is the
     * horizontal center point, not the left edge) over a landscape image.
     */
    private const CERTIFICATE_FIELD_DEFAULTS = [
        'name' => ['top' => 44, 'left' => 50, 'font_size' => 40, 'color' => '#1e293b'],
        'course' => ['top' => 58, 'left' => 50, 'font_size' => 22, 'color' => '#1e293b'],
        'date' => ['top' => 80, 'left' => 50, 'font_size' => 16, 'color' => '#475569'],
        'certificate_id' => ['top' => 92, 'left' => 50, 'font_size' => 12, 'color' => '#94a3b8'],
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(CourseLesson::class, CourseModule::class);
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(CourseQuizCheckpoint::class);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? Storage::disk('public')->url($this->thumbnail) : null;
    }

    public function hasCertificateTemplate(): bool
    {
        return (bool) $this->certificate_background;
    }

    public function certificateBackgroundUrl(): ?string
    {
        return $this->certificate_background ? Storage::disk('public')->url($this->certificate_background) : null;
    }

    /**
     * Stored per-field overrides merged over CERTIFICATE_FIELD_DEFAULTS, so
     * the admin only ever needs to fill in what they want to change.
     */
    public function certificateFields(): array
    {
        $stored = $this->certificate_fields ?? [];

        return collect(self::CERTIFICATE_FIELD_DEFAULTS)
            ->map(fn ($defaults, $key) => array_merge($defaults, $stored[$key] ?? []))
            ->all();
    }

    /**
     * Aggregate score for a user across every question in the course
     * (both mid-lesson checkpoint quizzes and module-level quizzes).
     */
    public function scoreFor(User $user): object
    {
        $questionIds = CourseQuizQuestion::whereIn(
            'quiz_checkpoint_id',
            $this->checkpoints()->pluck('id')
        )->pluck('id', 'id');

        if ($questionIds->isEmpty()) {
            return (object) [
                'percent' => null,
                'earned_points' => 0,
                'graded_points' => 0,
                'correct_count' => 0,
                'graded_count' => 0,
                'pending_count' => 0,
            ];
        }

        $answers = QuizAnswer::where('user_id', $user->id)
            ->whereIn('quiz_question_id', $questionIds)
            ->get();

        $pendingCount = $answers->whereNull('points_awarded')->count();
        $graded = $answers->whereNotNull('points_awarded');
        $gradedQuestionIds = $graded->pluck('quiz_question_id');

        $gradedPoints = (int) CourseQuizQuestion::whereIn('id', $gradedQuestionIds)->sum('points');
        $earnedPoints = (int) $graded->sum('points_awarded');
        $correctCount = $graded->where('is_correct', true)->count();

        $percent = $graded->isEmpty() ? null : (int) round($earnedPoints / max($gradedPoints, 1) * 100);

        return (object) [
            'percent' => $percent,
            'earned_points' => $earnedPoints,
            'graded_points' => $gradedPoints,
            'correct_count' => $correctCount,
            'graded_count' => $graded->count(),
            'pending_count' => $pendingCount,
        ];
    }
}

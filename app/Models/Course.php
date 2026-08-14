<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',

        // Legacy certificate fields
        'certificate_background',
        'certificate_fields',

        // New reusable certificate template
        'certificate_template_id',

        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'certificate_fields' => 'array',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)
            ->orderBy('sort_order');
    }


    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }


    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseLesson::class,
            CourseModule::class
        );
    }


    public function checkpoints(): HasMany
    {
        return $this->hasMany(CourseQuizCheckpoint::class);
    }


    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'course_user'
        )->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Certificate Template
    |--------------------------------------------------------------------------
    */

    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(
            CertificateTemplate::class,
            'certificate_template_id'
        );
    }


    /**
     * Check whether a reusable certificate template
     * has been assigned to this course.
     */
    public function hasCertificateTemplate(): bool
    {
        return ! empty($this->certificate_template_id);
    }


    /*
    |--------------------------------------------------------------------------
    | Thumbnail
    |--------------------------------------------------------------------------
    */

    public function thumbnailUrl(): ?string
    {
        if (empty($this->thumbnail)) {
            return null;
        }

        $path = ltrim(
            $this->thumbnail,
            '/'
        );

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        return asset(
            'storage/' . $path
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Legacy Certificate Background
    |--------------------------------------------------------------------------
    |
    | These methods are kept temporarily so old records/pages do not break.
    | The new certificate system uses certificateTemplate().
    |
    */

    public function certificateBackgroundUrl(): ?string
    {
        if (empty($this->certificate_background)) {
            return null;
        }

        $path = ltrim(
            $this->certificate_background,
            '/'
        );

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        return asset(
            'storage/' . $path
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Legacy Certificate Fields
    |--------------------------------------------------------------------------
    */

    public function certificateFields(): array
    {
        if (! is_array($this->certificate_fields)) {
            return [];
        }

        return $this->certificate_fields;
    }


    /*
    |--------------------------------------------------------------------------
    | Course Score
    |--------------------------------------------------------------------------
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


        $answers = QuizAnswer::where(
            'user_id',
            $user->id
        )
            ->whereIn(
                'quiz_question_id',
                $questionIds
            )
            ->get();


        $pendingCount = $answers
            ->whereNull('points_awarded')
            ->count();


        $graded = $answers
            ->whereNotNull('points_awarded');


        $gradedQuestionIds = $graded
            ->pluck('quiz_question_id');


        $gradedPoints = (int) CourseQuizQuestion::whereIn(
            'id',
            $gradedQuestionIds
        )->sum('points');


        $earnedPoints = (int) $graded
            ->sum('points_awarded');


        $correctCount = $graded
            ->where('is_correct', true)
            ->count();


        $percent = $graded->isEmpty()
            ? null
            : (int) round(
                $earnedPoints /
                max($gradedPoints, 1) *
                100
            );


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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceQuizAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'resource_checkpoint_id',
        'resource_quiz_question_id',
        'answer_text',
        'selected_option_ids',
        'is_correct',
        'points_awarded',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'is_correct' => 'boolean',
        'graded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ResourceQuizQuestion::class, 'resource_quiz_question_id');
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(ResourceCheckpoint::class, 'resource_checkpoint_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}

<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CourseLesson extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'course_module_id',
        'title',
        'description',
        'video_source',
        'video_path',
        'video_url',
        'duration',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'course_module_id';
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(CourseQuizCheckpoint::class)->orderBy('timestamp_seconds');
    }

    public function videoSrc(): ?string
    {
        if ($this->video_source === 'upload') {
            return $this->video_path ? Storage::disk('public')->url($this->video_path) : null;
        }

        return $this->video_url;
    }

    /**
     * Pulls a bare YouTube video ID out of either a plain URL or a
     * pasted <iframe> embed snippet, so the player only ever needs an ID.
     */
    public function youtubeId(): ?string
    {
        if ($this->video_source !== 'youtube' || ! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Parses the free-text duration label ("8:12" or "1:02:30") into seconds,
     * so checkpoint timestamps can be validated against it.
     */
    public function durationSeconds(): ?int
    {
        if (! $this->duration || ! preg_match('/^\d{1,2}(:\d{1,2}){1,2}$/', trim($this->duration))) {
            return null;
        }

        $parts = array_map('intval', explode(':', trim($this->duration)));

        return array_reduce($parts, fn ($carry, $part) => ($carry * 60) + $part, 0);
    }
}

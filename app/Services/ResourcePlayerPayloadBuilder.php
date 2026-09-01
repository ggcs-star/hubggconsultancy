<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\ResourceQuizAnswer;
use App\Models\User;
use App\Support\YoutubeUrl;
use Illuminate\Support\Collection;

class ResourcePlayerPayloadBuilder
{
    /**
     * Builds the per-resource JSON payload consumed by resources/js/resource-player.js
     * and the Alpine resourceLibrary() component in resources/views/user/resources/index.blade.php.
     * Mirrors App\Services\CoursePlayerPayloadBuilder's question/answer shape exactly, so the
     * shared user.courses._quiz-questions partial renders it without any changes.
     */
    public function build(Collection $resources, User $user): array
    {
        $checkpointIds = $resources->flatMap->checkpoints->pluck('id');

        $allQuestionIds = $resources->flatMap(
            fn (Resource $resource) => $resource->checkpoints->flatMap->questions
        )->pluck('id');

        $answersByQuestionId = ResourceQuizAnswer::where('user_id', $user->id)
            ->whereIn('resource_quiz_question_id', $allQuestionIds)
            ->get()
            ->keyBy('resource_quiz_question_id');

        return $resources->map(function (Resource $resource) use ($answersByQuestionId) {
            return [
                'id' => $resource->id,
                'title' => $resource->title,
                'english' => $this->buildLanguage($resource, 'english', $resource->english_youtube_url, $answersByQuestionId),
                'hindi' => $this->buildLanguage($resource, 'hindi', $resource->hindi_youtube_url, $answersByQuestionId),
                'gujarati' => $this->buildLanguage($resource, 'gujarati', $resource->gujarati_youtube_url, $answersByQuestionId),
            ];
        })->keyBy('id')->all();
    }

    private function buildLanguage(Resource $resource, string $language, ?string $url, Collection $answersByQuestionId): ?array
    {
        if (! $url) {
            return null;
        }

        $isPlaylist = YoutubeUrl::isPlaylist($url);
        $videoId = $isPlaylist ? null : YoutubeUrl::videoId($url);

        $checkpoints = $resource->checkpoints
            ->where('language', $language)
            ->sortBy('sort_order')
            ->values();

        return [
            'url' => $url,
            'isPlaylist' => $isPlaylist,
            'videoId' => $videoId,
            'checkpoints' => $checkpoints->map(fn ($checkpoint) => [
                'id' => $checkpoint->id,
                'title' => $checkpoint->title,
                'timestamp_seconds' => $checkpoint->timestamp_seconds,
                'answerUrl' => route('user.resource-quiz-answers.store', $checkpoint),
                'questions' => $this->buildQuestions($checkpoint->questions, $answersByQuestionId),
            ])->values(),
        ];
    }

    private function buildQuestions(Collection $questions, Collection $answersByQuestionId): array
    {
        return $questions->map(function ($question) use ($answersByQuestionId) {
            $answer = $answersByQuestionId->get($question->id);
            $answered = (bool) $answer;

            return [
                'id' => $question->id,
                'text' => $question->question_text,
                'points' => $question->points,
                'type' => $question->type,
                'options' => $question->options->map(fn ($option) => [
                    'id' => $option->id,
                    'text' => $option->option_text,
                ])->values(),
                'answered' => $answered,
                'isCorrect' => $answer?->is_correct,
                'pointsAwarded' => $answer?->points_awarded,
                'yourSelectedIds' => $answer?->selected_option_ids ?? [],
                'yourText' => $answer?->answer_text,
                'correctOptionIds' => ($answered && $question->type !== 'text')
                    ? $question->options->where('is_correct', true)->pluck('id')->values()
                    : [],
            ];
        })->values()->all();
    }
}

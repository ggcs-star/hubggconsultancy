<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLessonProgress;
use App\Models\QuizAnswer;
use App\Models\User;
use Illuminate\Support\Collection;

class CoursePlayerPayloadBuilder
{
    /**
     * Builds the JSON payload consumed by resources/js/course-player.js and
     * the Alpine courseCurriculumPlayer() component in
     * resources/views/user/courses/_player.blade.php.
     */
    public function build(Course $course, User $user, ?string $requestedItemKey = null): array
    {
        $course->load([
            'modules.lessons.checkpoints.questions.options',
            'modules.moduleQuizzes.questions.options',
        ]);

        $lessonIds = $course->modules->flatMap->lessons->pluck('id');
        $completedLessonIds = CourseLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('course_lesson_id', $lessonIds)
            ->pluck('course_lesson_id');

        $allQuestionIds = $course->modules->flatMap(function ($module) {
            return $module->lessons->flatMap(fn ($lesson) => $lesson->checkpoints->flatMap->questions)
                ->concat($module->moduleQuizzes->flatMap->questions);
        })->pluck('id');

        $answersByQuestionId = QuizAnswer::where('user_id', $user->id)
            ->whereIn('quiz_question_id', $allQuestionIds)
            ->get()
            ->keyBy('quiz_question_id');

        $items = [];
        $order = [];

        foreach ($course->modules as $module) {
            foreach ($module->orderedItems() as $item) {
                if ($item->item_type === 'lesson') {
                    $key = "lesson-{$item->id}";
                    $order[] = $key;
                    $items[$key] = [
                        'type' => 'lesson',
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'videoSource' => $item->video_source,
                        'videoSrc' => $item->video_source === 'youtube' ? $item->youtubeId() : $item->videoSrc(),
                        'duration' => $item->duration,
                        'completed' => $completedLessonIds->contains($item->id),
                        'progressUrl' => route('user.course-lesson-progress.store', $item),
                        'checkpoints' => $item->checkpoints->map(fn ($checkpoint) => [
                            'id' => $checkpoint->id,
                            'title' => $checkpoint->title,
                            'timestamp_seconds' => $checkpoint->timestamp_seconds,
                            'answerUrl' => route('user.course-quiz-answers.store', $checkpoint),
                            'questions' => $this->buildQuestions($checkpoint->questions, $answersByQuestionId),
                        ])->values(),
                    ];
                } else {
                    $key = "quiz-{$item->id}";
                    $order[] = $key;
                    $items[$key] = [
                        'type' => 'module_quiz',
                        'id' => $item->id,
                        'title' => $item->title,
                        'isRequired' => $item->is_required,
                        'completed' => $item->questions->isNotEmpty()
                            && $item->questions->every(fn ($question) => $answersByQuestionId->has($question->id)),
                        'answerUrl' => route('user.course-quiz-answers.store', $item),
                        'questions' => $this->buildQuestions($item->questions, $answersByQuestionId),
                    ];
                }
            }
        }

        $initialItemKey = ($requestedItemKey && isset($items[$requestedItemKey])) ? $requestedItemKey : ($order[0] ?? null);

        return [
            'items' => $items,
            'order' => $order,
            'initialItemKey' => $initialItemKey,
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
                // Only reveal correct options once the user has answered — never leak them up front.
                'correctOptionIds' => ($answered && $question->type !== 'text')
                    ? $question->options->where('is_correct', true)->pluck('id')->values()
                    : [],
            ];
        })->values()->all();
    }
}

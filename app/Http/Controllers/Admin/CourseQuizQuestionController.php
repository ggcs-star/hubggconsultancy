<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseQuizCheckpoint;
use App\Models\CourseQuizOption;
use App\Models\CourseQuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseQuizQuestionController extends Controller
{
    public function store(Request $request, CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question = CourseQuizQuestion::create([
            'quiz_checkpoint_id' => $checkpoint->id,
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $this->syncOptions($question, $data);

        return $this->redirectTo($checkpoint)->with('status', 'Question added.');
    }

    public function update(Request $request, CourseQuizQuestion $question): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question->update([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $question->options()->delete();
        $this->syncOptions($question, $data);

        return $this->redirectTo($question->checkpoint)->with('status', 'Question updated.');
    }

    public function destroy(CourseQuizQuestion $question): RedirectResponse
    {
        $checkpoint = $question->checkpoint;
        $question->delete();

        return $this->redirectTo($checkpoint)->with('status', 'Question deleted.');
    }

    private function validateQuestion(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:radio,checkbox,text'],
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
            'options' => ['required_unless:type,text', 'array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'correct' => ['required_unless:type,text', 'array'],
        ]);

        return $data;
    }

    private function syncOptions(CourseQuizQuestion $question, array $data): void
    {
        if ($data['type'] === 'text') {
            return;
        }

        $correct = array_map('strval', $data['correct'] ?? []);

        foreach ($data['options'] as $index => $text) {
            if ($text === null || trim($text) === '') {
                continue;
            }

            CourseQuizOption::create([
                'quiz_question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => in_array((string) $index, $correct, true),
            ]);
        }
    }

    private function redirectTo(CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        return $checkpoint->isModuleQuiz()
            ? redirect()->route('admin.course-module-quizzes.edit', $checkpoint)
            : redirect()->route('admin.course-lessons.edit', $checkpoint->course_lesson_id);
    }
}

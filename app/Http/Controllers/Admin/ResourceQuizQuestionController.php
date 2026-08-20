<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCheckpoint;
use App\Models\ResourceQuizOption;
use App\Models\ResourceQuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResourceQuizQuestionController extends Controller
{
    public function store(Request $request, ResourceCheckpoint $checkpoint): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question = ResourceQuizQuestion::create([
            'resource_checkpoint_id' => $checkpoint->id,
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $this->syncOptions($question, $data);

        return redirect()->route('admin.resources.show', $checkpoint->resource_id)->with('status', 'Question added.');
    }

    public function update(Request $request, ResourceQuizQuestion $question): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question->update([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $question->options()->delete();
        $this->syncOptions($question, $data);

        return redirect()->route('admin.resources.show', $question->checkpoint->resource_id)->with('status', 'Question updated.');
    }

    public function destroy(ResourceQuizQuestion $question): RedirectResponse
    {
        $resourceId = $question->checkpoint->resource_id;
        $question->delete();

        return redirect()->route('admin.resources.show', $resourceId)->with('status', 'Question deleted.');
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:radio,checkbox,text'],
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
            'options' => ['required_unless:type,text', 'array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'correct' => ['required_unless:type,text', 'array'],
        ]);
    }

    private function syncOptions(ResourceQuizQuestion $question, array $data): void
    {
        if ($data['type'] === 'text') {
            return;
        }

        $correct = array_map('strval', $data['correct'] ?? []);

        foreach ($data['options'] as $index => $text) {
            if ($text === null || trim($text) === '') {
                continue;
            }

            ResourceQuizOption::create([
                'resource_quiz_question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => in_array((string) $index, $correct, true),
            ]);
        }
    }
}

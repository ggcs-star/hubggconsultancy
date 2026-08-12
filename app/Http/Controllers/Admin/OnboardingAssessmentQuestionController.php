<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentOption;
use App\Models\OnboardingAssessmentQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnboardingAssessmentQuestionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question = OnboardingAssessmentQuestion::create([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $this->syncOptions($question, $data);

        return redirect()->route('admin.onboarding-assessment.index')->with('status', 'Question added.');
    }

    public function update(Request $request, OnboardingAssessmentQuestion $question): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question->update([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $question->options()->delete();
        $this->syncOptions($question, $data);

        return redirect()->route('admin.onboarding-assessment.index')->with('status', 'Question updated.');
    }

    public function destroy(OnboardingAssessmentQuestion $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('admin.onboarding-assessment.index')->with('status', 'Question deleted.');
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

    private function syncOptions(OnboardingAssessmentQuestion $question, array $data): void
    {
        if ($data['type'] === 'text') {
            return;
        }

        $correct = array_map('strval', $data['correct'] ?? []);

        foreach ($data['options'] as $index => $text) {
            if ($text === null || trim($text) === '') {
                continue;
            }

            OnboardingAssessmentOption::create([
                'onboarding_assessment_question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => in_array((string) $index, $correct, true),
            ]);
        }
    }
}

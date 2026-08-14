<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentAnswer;
use App\Models\OnboardingAssessmentQuestion;
use App\Models\OnboardingAssessmentSetting;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingAssessmentController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->profile_completed) {
            return view('user.onboarding-assessment.profile-required');
        }

        $settings = OnboardingAssessmentSetting::current();
        $score = $scorer->score($user);
        $questions = OnboardingAssessmentQuestion::with('options')->orderBy('sort_order')->get();

        $answers = [];
        if ($score->attempted) {
            $answers = $user->onboardingAssessmentAnswers()->get()->keyBy('onboarding_assessment_question_id');
        }

        return view('user.onboarding-assessment.index', [
            'settings' => $settings,
            'score' => $score,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }

    public function submit(Request $request, OnboardingAssessmentScorer $scorer): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->profile_completed) {
            return view('user.onboarding-assessment.profile-required');
        }

        $settings = OnboardingAssessmentSetting::current();

        if (! $settings->is_published) {
            return back()->with('status', 'This assessment is not available right now.');
        }

        if ($scorer->score($user)->attempted) {
            return back()->with('status', 'You have already submitted this assessment.');
        }

        $questions = OnboardingAssessmentQuestion::with('options')->orderBy('sort_order')->get();

        $data = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.selected' => ['nullable', 'array'],
            'answers.*.selected.*' => ['integer'],
            'answers.*.text' => ['nullable', 'string'],
        ]);

        foreach ($questions as $question) {
            $submitted = $data['answers'][$question->id] ?? [];
            $selectedIds = collect($submitted['selected'] ?? [])->map(fn ($id) => (int) $id)->values();

            $isCorrect = null;
            $pointsAwarded = null;

            if ($question->type !== 'text') {
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->sort()->values();
                $isCorrect = $selectedIds->sort()->values()->all() === $correctOptionIds->all();
                $pointsAwarded = $isCorrect ? $question->points : 0;
            }

            OnboardingAssessmentAnswer::updateOrCreate(
                ['user_id' => $user->id, 'onboarding_assessment_question_id' => $question->id],
                [
                    'answer_text' => $submitted['text'] ?? null,
                    'selected_option_ids' => $selectedIds->all(),
                    'is_correct' => $isCorrect,
                    'points_awarded' => $pointsAwarded,
                ]
            );
        }

        return redirect()->route('user.onboarding-assessment.index')->with('status', 'Assessment submitted.');
    }
}

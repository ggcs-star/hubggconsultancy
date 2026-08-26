<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentAnswer;
use App\Models\OnboardingAssessmentQuiz;
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
        $quizzes = $scorer->relevantQuizzes($user, withOptions: true);

        $answers = $score->attempted
            ? $user->onboardingAssessmentAnswers()->get()->keyBy('onboarding_assessment_question_id')
            : collect();

        $activeQuiz = $quizzes->firstWhere('id', (int) $request->query('quiz')) ?? $quizzes->first();

        return view('user.onboarding-assessment.index', [
            'settings' => $settings,
            'score' => $score,
            'quizzes' => $quizzes,
            'answers' => $answers,
            'activeQuiz' => $activeQuiz,
        ]);
    }

    public function results(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        $score = $scorer->score($request->user());

        return view('user.onboarding-assessment.results', [
            'score' => $score,
        ]);
    }

    public function submit(Request $request, OnboardingAssessmentQuiz $quiz, OnboardingAssessmentScorer $scorer): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->profile_completed) {
            return view('user.onboarding-assessment.profile-required');
        }

        $settings = OnboardingAssessmentSetting::current();

        if (! $settings->is_published || ! $quiz->is_published) {
            return back()->with('status', 'This assessment is not available right now.');
        }

        $questions = $quiz->questions()->with('options')->get();

        $alreadyAttempted = OnboardingAssessmentAnswer::where('user_id', $user->id)
            ->whereIn('onboarding_assessment_question_id', $questions->pluck('id'))
            ->exists();

        if ($alreadyAttempted) {
            return back()->with('status', "You have already submitted \"{$quiz->title}\".");
        }

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

            $optionsSnapshot = null;

            if ($question->type === 'checkbox') {
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id');

                // Partial credit: points are earned in proportion to how many of the
                // correct options were selected. Selecting a wrong option earns nothing
                // for it but is never penalized — no negative marking.
                $correctSelectedCount = $selectedIds->intersect($correctOptionIds)->count();
                $totalCorrectCount = $correctOptionIds->count();

                $isCorrect = $selectedIds->sort()->values()->all() === $correctOptionIds->sort()->values()->all();
                $pointsAwarded = $totalCorrectCount > 0
                    ? (int) round($question->points * ($correctSelectedCount / $totalCorrectCount))
                    : 0;
            } elseif ($question->type === 'radio') {
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->sort()->values();
                $isCorrect = $selectedIds->sort()->values()->all() === $correctOptionIds->all();
                $pointsAwarded = $isCorrect ? $question->points : 0;
            }

            if ($question->type !== 'text') {
                // Snapshot the exact option list as answered — options get wholesale
                // deleted+recreated on every question edit, so this is the only reliable
                // way to show "what the user actually saw" later, without duplicates.
                $optionsSnapshot = $question->options->map(fn ($option) => [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'selected' => $selectedIds->contains($option->id),
                ])->values()->all();
            }

            OnboardingAssessmentAnswer::updateOrCreate(
                ['user_id' => $user->id, 'onboarding_assessment_question_id' => $question->id],
                [
                    'answer_text' => $submitted['text'] ?? null,
                    'selected_option_ids' => $selectedIds->all(),
                    'is_correct' => $isCorrect,
                    'points_awarded' => $pointsAwarded,
                    'question_text' => $question->question_text,
                    'question_points' => $question->points,
                    'options_snapshot' => $optionsSnapshot,
                ]
            );
        }

        // Auto-advance to the next quiz the user hasn't attempted yet, so they don't have
        // to manually click through tabs. If everything is now attempted, land back on the
        // index without a quiz param — that's what triggers the "Assessment Completed" screen.
        $remainingQuizzes = $scorer->relevantQuizzes($user);
        $answeredQuestionIds = $user->onboardingAssessmentAnswers()->pluck('onboarding_assessment_question_id');

        $nextQuiz = $remainingQuizzes
            ->reject(fn (OnboardingAssessmentQuiz $candidate) => $candidate->id === $quiz->id)
            ->first(fn (OnboardingAssessmentQuiz $candidate) => $candidate->questions->isNotEmpty()
                && $candidate->questions->pluck('id')->diff($answeredQuestionIds)->isNotEmpty());

        return redirect()
            ->route('user.onboarding-assessment.index', $nextQuiz ? ['quiz' => $nextQuiz->id] : [])
            ->with('status', "\"{$quiz->title}\" submitted.");
    }
}

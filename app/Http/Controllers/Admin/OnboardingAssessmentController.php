<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentAnswer;
use App\Models\OnboardingAssessmentQuestion;
use App\Models\OnboardingAssessmentQuiz;
use App\Models\OnboardingAssessmentSetting;
use App\Models\User;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class OnboardingAssessmentController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        $tabs = ['quizzes', 'settings', 'results'];
        $activeTab = $request->query('tab', 'quizzes');
        if (! in_array($activeTab, $tabs, true)) {
            $activeTab = 'quizzes';
        }

        $settings = OnboardingAssessmentSetting::current();

        $quizzes = null;
        $selectedQuiz = null;
        $questionsPage = null;
        if ($activeTab === 'quizzes') {
            $quizzes = $this->quizzesForList($request);
            $selectedQuiz = $this->selectedQuiz($request, $quizzes);

            if ($selectedQuiz) {
                $questionsPage = $selectedQuiz->questions()
                    ->with('options')
                    ->orderBy('sort_order')
                    ->paginate(10, ['*'], 'qpage')
                    ->withQueryString();
            }
        }

        $results = null;
        if ($activeTab === 'results') {
            $results = $this->filteredResults($request, $scorer);
        }

        return view('admin.onboarding-assessment.index', [
            'activeTab' => $activeTab,
            'settings' => $settings,
            'quizzes' => $quizzes,
            'selectedQuiz' => $selectedQuiz,
            'questionsPage' => $questionsPage,
            'results' => $results,
        ]);
    }

    /**
     * @return Collection<int, OnboardingAssessmentQuiz>
     */
    private function quizzesForList(Request $request): Collection
    {
        $search = trim((string) $request->query('search'));

        $query = OnboardingAssessmentQuiz::withCount('questions')->withSum('questions', 'points')->ordered();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    private function selectedQuiz(Request $request, Collection $quizzes): ?OnboardingAssessmentQuiz
    {
        $requestedId = $request->integer('quiz');
        $requested = $requestedId ? $quizzes->firstWhere('id', $requestedId) : null;

        return $requested ?? $quizzes->first();
    }

    private function filteredResults(Request $request, OnboardingAssessmentScorer $scorer): LengthAwarePaginator
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');
        $minScore = $request->query('min_score');
        $maxScore = $request->query('max_score');
        $submittedFrom = $request->query('submitted_from');
        $submittedTo = $request->query('submitted_to');

        $students = User::where('role', 'user')
            ->with('interests')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $rows = $students->map(fn (User $user) => [
            'user' => $user,
            'score' => $scorer->score($user),
        ]);

        if ($status !== '') {
            $rows = $rows->filter(fn (array $row) => $row['score']->status === $status);
        }

        if ($minScore !== null && $minScore !== '') {
            $rows = $rows->filter(fn (array $row) => $row['score']->percent !== null && $row['score']->percent >= (int) $minScore);
        }

        if ($maxScore !== null && $maxScore !== '') {
            $rows = $rows->filter(fn (array $row) => $row['score']->percent !== null && $row['score']->percent <= (int) $maxScore);
        }

        if ($submittedFrom) {
            $rows = $rows->filter(fn (array $row) => $row['score']->submitted_at && $row['score']->submitted_at->toDateString() >= $submittedFrom);
        }

        if ($submittedTo) {
            $rows = $rows->filter(fn (array $row) => $row['score']->submitted_at && $row['score']->submitted_at->toDateString() <= $submittedTo);
        }

        $rows = $rows->values();

        $perPage = 10;
        $page = $request->integer('page', 1);

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'passing_score_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        OnboardingAssessmentSetting::current()->update($data);

        return back()->with('status', 'Assessment settings saved.');
    }

    public function resultShow(User $user, OnboardingAssessmentScorer $scorer): View
    {
        $quizzes = $scorer->relevantQuizzes($user, withOptions: true);
        $answers = OnboardingAssessmentAnswer::where('user_id', $user->id)
            ->get()
            ->keyBy('onboarding_assessment_question_id');

        return view('admin.onboarding-assessment.results-show', [
            'student' => $user,
            'score' => $scorer->score($user),
            'quizzes' => $quizzes,
            'answers' => $answers,
        ]);
    }

    public function retake(User $user): RedirectResponse
    {
        // withTrashed() matters here: a quiz's questions get soft-deleted whenever they're
        // edited/replaced, but the user's old answers still reference those question ids —
        // without it, "Retake All" would silently leave stale answers behind.
        OnboardingAssessmentAnswer::where('user_id', $user->id)
            ->whereIn('onboarding_assessment_question_id', OnboardingAssessmentQuestion::withTrashed()->whereNotNull('onboarding_assessment_quiz_id')->pluck('id'))
            ->delete();

        return redirect()
            ->route('admin.onboarding-assessment.index', ['tab' => 'results'])
            ->with('status', "{$user->name} can now retake the assessment.");
    }

    public function retakeQuiz(User $user, OnboardingAssessmentQuiz $quiz): RedirectResponse
    {
        OnboardingAssessmentAnswer::where('user_id', $user->id)
            ->whereIn('onboarding_assessment_question_id', $quiz->questions()->withTrashed()->pluck('id'))
            ->delete();

        return redirect()
            ->back()
            ->with('status', "{$user->name} can now retake \"{$quiz->title}\".");
    }
}

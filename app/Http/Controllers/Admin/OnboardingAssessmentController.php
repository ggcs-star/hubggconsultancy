<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentAnswer;
use App\Models\OnboardingAssessmentQuestion;
use App\Models\OnboardingAssessmentSetting;
use App\Models\User;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class OnboardingAssessmentController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        $tabs = ['manage', 'results'];
        $activeTab = $request->query('tab', 'manage');
        if (! in_array($activeTab, $tabs, true)) {
            $activeTab = 'manage';
        }

        $settings = OnboardingAssessmentSetting::current();
        $questions = OnboardingAssessmentQuestion::with('options')->orderBy('sort_order')->get();

        $results = null;
        if ($activeTab === 'results') {
            $results = $this->filteredResults($request, $scorer);
        }

        return view('admin.onboarding-assessment.index', [
            'activeTab' => $activeTab,
            'settings' => $settings,
            'questions' => $questions,
            'results' => $results,
        ]);
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

        $perPage = 15;
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'passing_score_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        OnboardingAssessmentSetting::current()->update($data);

        return back()->with('status', 'Assessment settings saved.');
    }

    public function resultShow(User $user, OnboardingAssessmentScorer $scorer): View
    {
        $questions = OnboardingAssessmentQuestion::with('options')->orderBy('sort_order')->get();
        $answers = OnboardingAssessmentAnswer::where('user_id', $user->id)
            ->get()
            ->keyBy('onboarding_assessment_question_id');

        return view('admin.onboarding-assessment.results-show', [
            'student' => $user,
            'score' => $scorer->score($user),
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }

    public function retake(User $user): RedirectResponse
    {
        OnboardingAssessmentAnswer::where('user_id', $user->id)->delete();

        return redirect()
            ->route('admin.onboarding-assessment.index', ['tab' => 'results'])
            ->with('status', "{$user->name} can now retake the assessment.");
    }
}

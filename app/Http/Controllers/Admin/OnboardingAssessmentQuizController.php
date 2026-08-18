<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingAssessmentQuiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnboardingAssessmentQuizController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateQuiz($request);
        $data['is_published'] = $request->boolean('is_published');

        $quiz = OnboardingAssessmentQuiz::create($data);

        return redirect()
            ->route('admin.onboarding-assessment.index', ['tab' => 'quizzes', 'quiz' => $quiz->id])
            ->with('status', 'Quiz added.');
    }

    public function update(Request $request, OnboardingAssessmentQuiz $quiz): RedirectResponse
    {
        $data = $this->validateQuiz($request);
        $data['is_published'] = $request->boolean('is_published');

        $quiz->update($data);

        return redirect()
            ->route('admin.onboarding-assessment.index', ['tab' => 'quizzes', 'quiz' => $quiz->id])
            ->with('status', 'Quiz updated.');
    }

    public function destroy(OnboardingAssessmentQuiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('admin.onboarding-assessment.index', ['tab' => 'quizzes'])->with('status', 'Quiz deleted.');
    }

    public function togglePublished(Request $request, OnboardingAssessmentQuiz $quiz): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $quiz->update($data);

        return back()->with('status', $quiz->is_published ? "\"{$quiz->title}\" published." : "\"{$quiz->title}\" set to draft.");
    }

    /**
     * Reorders quizzes. Body: { ids: [quizId, quizId, ...] }
     */
    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            OnboardingAssessmentQuiz::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function validateQuiz(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }
}

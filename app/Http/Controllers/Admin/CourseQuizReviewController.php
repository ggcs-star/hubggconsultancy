<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseQuizReviewController extends Controller
{
    public function index(): View
    {
        $pendingAnswers = QuizAnswer::whereNull('points_awarded')
            ->whereNotNull('answer_text')
            ->with(['user', 'question.checkpoint.lesson', 'question.checkpoint.course'])
            ->latest()
            ->paginate(10);

        return view('admin.courses.quiz-review.index', [
            'pendingAnswers' => $pendingAnswers,
        ]);
    }

    public function grade(Request $request, QuizAnswer $answer): RedirectResponse
    {
        $data = $request->validate([
            'points_awarded' => ['required', 'integer', 'min:0', 'max:' . $answer->question->points],
        ]);

        $answer->update([
            'points_awarded' => $data['points_awarded'],
            'is_correct' => $data['points_awarded'] >= $answer->question->points,
            'graded_by' => $request->user()->id,
            'graded_at' => now(),
        ]);

        return back()->with('status', 'Grade saved.');
    }
}

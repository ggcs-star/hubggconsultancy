<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ResourceCheckpoint;
use App\Models\ResourceQuizAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceQuizAnswerController extends Controller
{
    public function store(Request $request, ResourceCheckpoint $checkpoint): JsonResponse
    {
        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'selected_option_ids' => ['nullable', 'array'],
            'selected_option_ids.*' => ['integer'],
            'answer_text' => ['nullable', 'string'],
        ]);

        $question = $checkpoint->questions()->with('options')->findOrFail($data['question_id']);
        $selectedIds = collect($data['selected_option_ids'] ?? [])->map(fn ($id) => (int) $id)->values();

        $isCorrect = null;
        $pointsAwarded = null;
        $correctOptionIds = [];

        if ($question->type !== 'text') {
            $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->values();
            $isCorrect = $selectedIds->sort()->values()->all() === $correctOptionIds->sort()->values()->all();
            $pointsAwarded = $isCorrect ? $question->points : 0;
        }

        $answer = ResourceQuizAnswer::updateOrCreate(
            ['user_id' => $request->user()->id, 'resource_quiz_question_id' => $question->id],
            [
                'resource_checkpoint_id' => $checkpoint->id,
                'answer_text' => $data['answer_text'] ?? null,
                'selected_option_ids' => $selectedIds->all(),
                'is_correct' => $isCorrect,
                'points_awarded' => $pointsAwarded,
            ]
        );

        return response()->json([
            'is_correct' => $answer->is_correct,
            'points_awarded' => $answer->points_awarded,
            'correct_option_ids' => $question->type !== 'text' ? $correctOptionIds : [],
        ]);
    }
}

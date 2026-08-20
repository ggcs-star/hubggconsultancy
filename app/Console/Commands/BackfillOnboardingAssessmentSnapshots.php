<?php

namespace App\Console\Commands;

use App\Models\OnboardingAssessmentAnswer;
use App\Models\OnboardingAssessmentOption;
use Illuminate\Console\Command;

class BackfillOnboardingAssessmentSnapshots extends Command
{
    protected $signature = 'onboarding-assessment:backfill-snapshots';

    protected $description = 'Freeze question_text/question_points/options_snapshot on every answer that predates the freeze-on-submit fix, so it stops following live question edits.';

    public function handle(): int
    {
        $answers = OnboardingAssessmentAnswer::whereNull('question_text')->get();
        $this->info("Answers missing a snapshot: {$answers->count()}");

        $done = 0;

        foreach ($answers as $answer) {
            $question = $answer->question; // relation is ->withTrashed()

            if (! $question) {
                continue;
            }

            $optionsSnapshot = null;

            if ($question->type !== 'text') {
                $selectedIds = collect($answer->selected_option_ids ?? []);
                $liveOptions = $question->options()->orderBy('sort_order')->get();
                $liveTexts = $liveOptions->pluck('option_text')->map(fn ($t) => trim(mb_strtolower($t)))->all();

                $selectedTrashed = OnboardingAssessmentOption::withTrashed()->whereIn('id', $selectedIds)->get();
                $matchedLiveIds = collect();

                foreach ($selectedTrashed as $selected) {
                    $norm = trim(mb_strtolower($selected->option_text));
                    $match = $liveOptions->first(fn ($live) => trim(mb_strtolower($live->option_text)) === $norm);

                    if ($match) {
                        $matchedLiveIds->push($match->id);
                    }
                }

                $snapshot = $liveOptions->map(fn ($option) => [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'selected' => $selectedIds->contains($option->id) || $matchedLiveIds->contains($option->id),
                ]);

                // A selected option with no live text match at all — genuinely removed/renamed.
                $unmatched = $selectedTrashed
                    ->reject(fn ($selected) => in_array(trim(mb_strtolower($selected->option_text)), $liveTexts, true))
                    ->map(fn ($option) => [
                        'id' => $option->id,
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'selected' => true,
                    ]);

                $optionsSnapshot = $snapshot->concat($unmatched)->values()->all();
            }

            $answer->forceFill([
                'question_text' => $question->question_text,
                'question_points' => $question->points,
                'options_snapshot' => $optionsSnapshot,
            ])->save();

            $done++;
        }

        $this->info("Backfilled: {$done}");

        return self::SUCCESS;
    }
}

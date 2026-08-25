<?php

namespace Database\Seeders;

use App\Models\Contest;
use App\Models\ContestAchievement;
use App\Models\OnboardingAssessmentAnswer;
use App\Models\OnboardingAssessmentQuestion;
use App\Models\OnboardingAssessmentQuiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo data covering the full sales-contest flow end to end:
 * three approved salespersons (Rahul, Amit, Priya) each with their own
 * referred team (2 / 3 / 1 members), each having taken the onboarding
 * assessment with a different score profile, and each participating in
 * "GG Prime Mega Contest" with different achievement totals — matching
 * the 🥇🥈🥉 88% / 72% / 60% example used to design the Contest Tracker.
 *
 * Safe to re-run: every write is an updateOrCreate/firstOrCreate keyed
 * on a natural unique field, so running this twice does not duplicate data.
 */
class DemoSalesScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $rahul = $this->makeApprovedUser('Rahul Sharma', 'rahul.sharma@demo.ggconsultancy.test');
        $amit = $this->makeApprovedUser('Amit Verma', 'amit.verma@demo.ggconsultancy.test');
        $priya = $this->makeApprovedUser('Priya Singh', 'priya.singh@demo.ggconsultancy.test');

        // Teams: Rahul has 2 members, Amit has 3, Priya has 1.
        foreach (range(1, 2) as $i) {
            $this->makeApprovedUser("Rahul's Team Member {$i}", "rahul.team{$i}@demo.ggconsultancy.test", $rahul->id);
        }
        foreach (range(1, 3) as $i) {
            $this->makeApprovedUser("Amit's Team Member {$i}", "amit.team{$i}@demo.ggconsultancy.test", $amit->id);
        }
        $this->makeApprovedUser("Priya's Team Member 1", 'priya.team1@demo.ggconsultancy.test', $priya->id);

        // Contest: reuse the existing "GG Prime Mega Contest" if present, only
        // fixing its target_value (it was created before that field existed).
        $contest = Contest::firstOrCreate(
            ['name' => 'GG Prime Mega Contest'],
            [
                'target_type' => 'sales',
                'target' => '25 lakh',
                'target_value' => 2500000,
                'participation_type' => 'individual',
                'participant_mode' => 'open',
                'starts_at' => now()->startOfMonth(),
                'ends_at' => now()->startOfMonth()->addDays(28),
                'reward' => '25,000 bonus',
                'reward_type' => 'bonus',
                'reward_second' => '15,000 bonus',
                'reward_third' => '10,000 bonus',
                'is_active' => true,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ]
        );

        if ((float) $contest->target_value <= 0) {
            $contest->update(['target_value' => 2500000]);
        }

        foreach ([$rahul->id, $amit->id, $priya->id] as $userId) {
            $contest->participants()->syncWithoutDetaching([$userId]);
        }

        // 88% of 25,00,000 = 22,00,000
        $this->logAchievements($contest, $rahul, $admin, [500000, 800000, 900000]);
        // 72% of 25,00,000 = 18,00,000
        $this->logAchievements($contest, $amit, $admin, [700000, 600000, 500000]);
        // 60% of 25,00,000 = 15,00,000
        $this->logAchievements($contest, $priya, $admin, [600000, 500000, 400000]);

        // Onboarding assessment — three different score/completion profiles.
        // Rahul: strong, one missed question, fully graded.
        $this->seedExam(
            user: $rahul,
            admin: $admin,
            wrongObjectiveIds: [55],
            textGrades: [61 => 5, 56 => 5, 66 => 5],
        );

        // Amit: mid-range, one text answer still awaiting review (pending_review status).
        $this->seedExam(
            user: $amit,
            admin: $admin,
            wrongObjectiveIds: [13, 17, 59, 64, 54],
            textGrades: [61 => null, 56 => 4, 66 => 5],
        );

        // Priya: lower score, and hasn't attempted the last quiz yet (in_progress status).
        $this->seedExam(
            user: $priya,
            admin: $admin,
            wrongObjectiveIds: [13, 17, 58, 60, 52],
            textGrades: [61 => 3, 56 => null],
            skipQuizIds: [20],
        );
    }

    private function makeApprovedUser(string $name, string $email, ?int $referredBy = null): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'role' => 'user',
                'salesperson_status' => 'approved',
                'profile_completed' => true,
                'referred_by' => $referredBy,
            ]
        );
    }

    private function logAchievements(Contest $contest, User $user, ?User $admin, array $amounts): void
    {
        foreach ($amounts as $index => $amount) {
            ContestAchievement::firstOrCreate(
                [
                    'contest_id' => $contest->id,
                    'user_id' => $user->id,
                    'note' => 'Sale ' . ($index + 1),
                ],
                [
                    'amount' => $amount,
                    'created_by' => $admin?->id,
                ]
            );
        }
    }

    private function seedExam(
        User $user,
        ?User $admin,
        array $wrongObjectiveIds,
        array $textGrades,
        array $skipQuizIds = [],
    ): void {
        $quizzes = OnboardingAssessmentQuiz::whereIn('id', [4, 5, 6, 18, 19, 20])
            ->whereNotIn('id', $skipQuizIds)
            ->with('questions.options')
            ->get();

        foreach ($quizzes as $quiz) {
            foreach ($quiz->questions as $question) {
                if ($question->type === 'text') {
                    $this->answerText($user, $question, $textGrades[$question->id] ?? null);

                    continue;
                }

                $this->answerObjective($user, $question, ! in_array($question->id, $wrongObjectiveIds, true));
            }
        }
    }

    private function answerObjective(User $user, OnboardingAssessmentQuestion $question, bool $isCorrect): void
    {
        $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->sort()->values();
        $wrongOptionIds = $question->options->where('is_correct', false)->pluck('id')->sort()->values();

        $selectedIds = $isCorrect
            ? $correctOptionIds
            : ($wrongOptionIds->isNotEmpty() ? $wrongOptionIds->take(1)->values() : collect());

        $optionsSnapshot = $question->options->map(fn ($option) => [
            'id' => $option->id,
            'option_text' => $option->option_text,
            'is_correct' => $option->is_correct,
            'selected' => $selectedIds->contains($option->id),
        ])->values()->all();

        OnboardingAssessmentAnswer::updateOrCreate(
            ['user_id' => $user->id, 'onboarding_assessment_question_id' => $question->id],
            [
                'answer_text' => null,
                'selected_option_ids' => $selectedIds->values()->all(),
                'is_correct' => $isCorrect,
                'points_awarded' => $isCorrect ? $question->points : 0,
                'question_text' => $question->question_text,
                'question_points' => $question->points,
                'options_snapshot' => $optionsSnapshot,
            ]
        );
    }

    private function answerText(User $user, OnboardingAssessmentQuestion $question, ?int $pointsAwarded): void
    {
        OnboardingAssessmentAnswer::updateOrCreate(
            ['user_id' => $user->id, 'onboarding_assessment_question_id' => $question->id],
            [
                'answer_text' => 'In my own words: I broke the concept down into a simple analogy the client already understood, then connected it back to their specific goal.',
                'selected_option_ids' => [],
                'is_correct' => null,
                'points_awarded' => $pointsAwarded,
                'question_text' => $question->question_text,
                'question_points' => $question->points,
                'options_snapshot' => null,
            ]
        );
    }
}

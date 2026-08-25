<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Contest;
use App\Models\Course;
use App\Models\Event;
use App\Models\IncentiveEntry;
use App\Models\Lead;
use App\Models\OnboardingAssessmentAnswer;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'topPerformers' => $this->topPerformers(),
            'trainingProgress' => $this->teamTrainingProgress(),
            'leadStats' => $this->leadStats(),
            'contestProgress' => $this->contestProgress(),
            'tasks' => $this->tasks(),
            'upcomingEvents' => Event::published()->upcoming()->take(3)->get(),
            'announcements' => Announcement::visible()->latest('published_at')->latest('id')->take(3)->get(),
            'learningProgress' => $this->teamLearningProgress(),
        ]);
    }

    private function topPerformers(int $limit = 5): \Illuminate\Support\Collection
    {
        return User::where('role', 'user')->get()
            ->map(fn (User $user) => (object) ['user' => $user, 'points' => $user->totalPoints()])
            ->sortByDesc('points')
            ->values()
            ->take($limit)
            ->map(function ($row, $index) {
                $row->rank = $index + 1;

                return $row;
            });
    }

    private function teamTrainingProgress(): object
    {
        $courses = Course::where('is_published', true)->with('assignedUsers')->get();

        $percents = [];

        foreach ($courses as $course) {
            foreach ($course->assignedUsers as $user) {
                $percents[] = $course->progressFor($user)->percent;
            }
        }

        return (object) [
            'percent' => count($percents) > 0 ? (int) round(array_sum($percents) / count($percents)) : 0,
            'has_data' => count($percents) > 0,
        ];
    }

    private function leadStats(): object
    {
        $leads = Lead::all();

        $newThisWeek = $leads->where('created_at', '>=', now()->subDays(7))->count();

        $wonThisMonth = $leads->filter(fn (Lead $lead) => $lead->won_at && $lead->won_at->isSameMonth(now()));
        $wonLastMonth = $leads->filter(fn (Lead $lead) => $lead->won_at && $lead->won_at->isSameMonth(now()->subMonthNoOverflow()));

        $salesThisMonth = (float) $wonThisMonth->sum('expected_value');
        $salesLastMonth = (float) $wonLastMonth->sum('expected_value');

        $payoutsThisMonth = $this->payoutsFor(now());
        $payoutsLastMonth = $this->payoutsFor(now()->subMonthNoOverflow());

        return (object) [
            'total' => $leads->count(),
            'new_this_week' => $newThisWeek,
            'sales_this_month' => $salesThisMonth,
            'sales_change_percent' => $salesLastMonth > 0 ? (int) round(($salesThisMonth - $salesLastMonth) / $salesLastMonth * 100) : null,
            'payouts_this_month' => $payoutsThisMonth,
            'payouts_change_percent' => $payoutsLastMonth > 0 ? (int) round(($payoutsThisMonth - $payoutsLastMonth) / $payoutsLastMonth * 100) : null,
        ];
    }

    private function payoutsFor(\Illuminate\Support\Carbon $month): float
    {
        return (float) IncentiveEntry::whereYear('awarded_at', $month->year)
            ->whereMonth('awarded_at', $month->month)
            ->sum('amount');
    }

    private function contestProgress(): ?object
    {
        $contest = Contest::active()->withCount('registrations')->orderByDesc('starts_at')->first();

        if (! $contest) {
            return null;
        }

        return (object) [
            'contest' => $contest,
            'leader' => $contest->rankedParticipants()->first(),
            'total_participants' => $contest->registrations_count,
        ];
    }

    private function tasks(): array
    {
        return [
            [
                'label' => 'Unassigned leads',
                'count' => Lead::whereNull('assigned_to')->count(),
                'route' => route('admin.leads.index'),
            ],
            [
                'label' => 'Leads overdue for follow-up',
                'count' => Lead::needsFollowUp()->count(),
                'route' => route('admin.leads.index'),
            ],
            [
                'label' => 'Pending salesperson applications',
                'count' => User::where('salesperson_status', 'pending')->count(),
                'route' => route('admin.salesperson-applications'),
            ],
            [
                'label' => 'Text answers awaiting grading',
                'count' => OnboardingAssessmentAnswer::whereNull('points_awarded')->whereNull('is_correct')->count(),
                'route' => route('admin.onboarding-assessment.index', ['tab' => 'results']),
            ],
        ];
    }

    private function teamLearningProgress(int $limit = 4): \Illuminate\Support\Collection
    {
        return Course::where('is_published', true)->with('assignedUsers')->orderBy('title')->get()
            ->filter(fn (Course $course) => $course->assignedUsers->isNotEmpty())
            ->map(function (Course $course) {
                $percents = $course->assignedUsers->map(fn (User $user) => $course->progressFor($user)->percent);
                $course->progress = (object) ['percent' => (int) round($percents->avg())];

                return $course;
            })
            ->take($limit);
    }
}

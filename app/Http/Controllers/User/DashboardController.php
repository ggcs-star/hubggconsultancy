<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Contest;
use App\Models\Course;
use App\Models\Event;
use App\Models\IncentiveEntry;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'user' => $user,
            'rank' => $this->rankFor($user),
            'trainingProgress' => $this->trainingProgress($user),
            'leadStats' => $this->leadStats($user),
            'contestProgress' => $this->contestProgress($user),
            'tasks' => $this->tasks($user),
            'upcomingEvents' => Event::published()->upcoming()->take(3)->get(),
            'announcements' => Announcement::visible()->latest('published_at')->latest('id')->take(3)->get(),
            'learningProgress' => $this->learningProgress($user),
            'topPerformers' => $this->topPerformers($user),
        ]);
    }

    private function rankFor(User $user): object
    {
        $ranked = User::where('role', 'user')->get()
            ->map(fn (User $candidate) => (object) ['user' => $candidate, 'points' => $candidate->totalPoints()])
            ->sortByDesc('points')
            ->values();

        $position = $ranked->search(fn ($row) => $row->user->id === $user->id);

        return (object) [
            'position' => $position === false ? null : $position + 1,
            'total' => $ranked->count(),
            'points' => $user->totalPoints(),
            'tier' => $user->tier(),
        ];
    }

    private function trainingProgress(User $user): object
    {
        $courses = $user->assignedCourses()->where('is_published', true)->get();

        if ($courses->isEmpty()) {
            return (object) ['percent' => 0, 'has_courses' => false];
        }

        $percent = (int) round($courses->map(fn (Course $course) => $course->progressFor($user)->percent)->avg());

        return (object) ['percent' => $percent, 'has_courses' => true];
    }

    private function leadStats(User $user): object
    {
        $leads = Lead::assignedTo($user->id)->get();

        $newThisWeek = $leads->where('created_at', '>=', now()->subDays(7))->count();

        $wonThisMonth = $leads->filter(fn (Lead $lead) => $lead->won_at && $lead->won_at->isSameMonth(now()));
        $wonLastMonth = $leads->filter(fn (Lead $lead) => $lead->won_at && $lead->won_at->isSameMonth(now()->subMonthNoOverflow()));

        $salesThisMonth = (float) $wonThisMonth->sum('expected_value');
        $salesLastMonth = (float) $wonLastMonth->sum('expected_value');

        return (object) [
            'total' => $leads->count(),
            'new_this_week' => $newThisWeek,
            'sales_this_month' => $salesThisMonth,
            'sales_change_percent' => $salesLastMonth > 0 ? (int) round(($salesThisMonth - $salesLastMonth) / $salesLastMonth * 100) : null,
            'earnings_this_month' => $this->earningsFor($user, now()),
            'earnings_change_percent' => $this->earningsChangePercent($user),
        ];
    }

    private function earningsFor(User $user, \Illuminate\Support\Carbon $month): float
    {
        return (float) IncentiveEntry::where('user_id', $user->id)
            ->whereYear('awarded_at', $month->year)
            ->whereMonth('awarded_at', $month->month)
            ->sum('amount');
    }

    private function earningsChangePercent(User $user): ?int
    {
        $thisMonth = $this->earningsFor($user, now());
        $lastMonth = $this->earningsFor($user, now()->subMonthNoOverflow());

        return $lastMonth > 0 ? (int) round(($thisMonth - $lastMonth) / $lastMonth * 100) : null;
    }

    private function contestProgress(User $user): ?object
    {
        $contest = Contest::active()
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->orderByDesc('starts_at')
            ->first();

        if (! $contest) {
            return null;
        }

        return (object) [
            'contest' => $contest,
            'achieved' => $contest->totalAchievementFor($user),
            'percent' => $contest->progressPercentFor($user),
            'rank' => $contest->rankedParticipants()->firstWhere('id', $user->id)?->rank,
            'total_participants' => $contest->participants()->count(),
        ];
    }

    private function tasks(User $user): array
    {
        $leads = Lead::assignedTo($user->id)->get();

        return [
            [
                'label' => 'Follow up with leads',
                'count' => $leads->filter(fn (Lead $lead) => $lead->isOverdue() || ($lead->next_follow_up_at && $lead->next_follow_up_at->isToday()))->count(),
                'route' => route('user.leads.index'),
            ],
            [
                'label' => 'New leads need action',
                'count' => $leads->where('status', 'new')->count(),
                'route' => route('user.leads.index', ['status' => 'new']),
            ],
            [
                'label' => 'Training modules to complete',
                'count' => $user->assignedCourses()->where('is_published', true)->get()->filter(fn (Course $course) => $course->progressFor($user)->percent < 100)->count(),
                'route' => route('user.learning-progress.index'),
            ],
            [
                'label' => 'Active contests to join',
                'count' => Contest::active()->where('participant_mode', 'open')
                    ->whereDoesntHave('participants', fn ($query) => $query->where('users.id', $user->id))
                    ->count(),
                'route' => route('user.contests.index'),
            ],
        ];
    }

    private function learningProgress(User $user): \Illuminate\Support\Collection
    {
        return $user->assignedCourses()->where('is_published', true)->orderBy('title')->get()
            ->map(function (Course $course) use ($user) {
                $course->progress = $course->progressFor($user);

                return $course;
            })
            ->take(4);
    }

    private function topPerformers(User $user): object
    {
        $ranked = User::where('role', 'user')->get()
            ->map(fn (User $candidate) => (object) ['user' => $candidate, 'points' => $candidate->totalPoints()])
            ->sortByDesc('points')
            ->values()
            ->map(function ($row, $index) {
                $row->rank = $index + 1;

                return $row;
            });

        $top = $ranked->take(3);
        $me = $ranked->firstWhere('user.id', $user->id);

        return (object) [
            'top' => $top,
            'me' => $me && $me->rank > 3 ? $me : null,
        ];
    }
}

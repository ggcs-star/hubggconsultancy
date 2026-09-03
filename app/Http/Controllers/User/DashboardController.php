<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Contest;
use App\Models\Course;
use App\Models\Event;
use App\Models\Lead;
use App\Models\User;
use App\Services\TeamApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private TeamApiService $teamApi)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'user' => $user,
            'rank' => $this->rankFor($user),
            'trainingProgress' => $this->trainingProgress($user),
            'leadStats' => $this->leadStats($user),
            'totalIncome' => $this->totalIncomeFor($user),
            'contestProgress' => $this->contestProgress($user),
            'myTasks' => $user->tasks()
                ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->orderBy('date')->orderBy('time')->get(),
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
        ];
    }

    /**
     * Lifetime income as reported by GG Prime itself (the `total_income`
     * field on /member/profile) — cached briefly since the dashboard is
     * high-traffic and this figure doesn't need to be second-by-second
     * fresh. Returns null (rendered as "—") if the user isn't linked to a
     * GG Prime account yet or the API can't be reached.
     */
    private function totalIncomeFor(User $user): ?float
    {
        if (! $user->gg_user_id) {
            return null;
        }

        return Cache::remember("gg_total_income_{$user->id}", now()->addMinutes(10), function () use ($user) {
            $result = $this->teamApi->profile(['user_id' => $user->gg_user_id]);

            return $result->status === 'found' ? (float) ($result->data['total_income'] ?? 0) : null;
        });
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

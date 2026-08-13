<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'pending_applications' => User::where('salesperson_status', 'pending')->count(),
            'approved_salespeople' => User::where('salesperson_status', 'approved')->count(),
            'profile_completed' => User::where('role', 'user')->where('profile_completed', true)->count(),
            'total_courses' => Course::where('is_published', true)->count(),
            'total_enrollments' => DB::table('course_user')->count(),
            'certificates_issued' => Certificate::count(),
            'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_for_user'])->count(),
        ];

        $recentUsers = User::where('role', 'user')->latest()->take(6)->get();
        $recentTickets = SupportTicket::with(['user', 'issueType'])->latest()->take(6)->get();
        $topUsers = $this->topUsersByPoints();

        $signups = $this->signupsChart();
        $topCourses = $this->topCoursesChart();
        $applicationStatus = $this->applicationStatusChart();
        $ticketStatus = $this->ticketStatusChart();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentTickets',
            'topUsers',
            'signups',
            'topCourses',
            'applicationStatus',
            'ticketStatus'
        ));
    }

    /**
     * Top N users by LMS quiz points earned (course checkpoint/module
     * quizzes only — same scope as User::lmsPoints(), which deliberately
     * excludes the separate Onboarding Assessment).
     */
    private function topUsersByPoints(int $limit = 5): \Illuminate\Support\Collection
    {
        $earnedByUser = DB::table('client_quiz_answers')
            ->whereNotNull('points_awarded')
            ->select('user_id', DB::raw('SUM(points_awarded) as earned'))
            ->groupBy('user_id')
            ->orderByDesc('earned')
            ->take($limit)
            ->pluck('earned', 'user_id');

        if ($earnedByUser->isEmpty()) {
            return collect();
        }

        $users = User::whereIn('id', $earnedByUser->keys())->get()->keyBy('id');

        return $earnedByUser->map(function ($earned, $userId) use ($users) {
            $user = $users->get($userId);

            if (!$user) {
                return null;
            }

            $points = $user->lmsPoints();

            return (object) [
                'user' => $user,
                'earned' => (int) $earned,
                'total' => $points->total,
                'percent' => $points->percent,
            ];
        })->filter()->values();
    }

    /**
     * Signups over the last 6 calendar months, zero-filled so gaps
     * don't just disappear from the line chart.
     */
    private function signupsChart(): array
    {
        $start = now()->subMonths(5)->startOfMonth();

        $counts = User::where('role', 'user')
            ->where('created_at', '>=', $start)
            ->get()
            ->groupBy(fn ($user) => $user->created_at->format('Y-m'))
            ->map->count();

        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function topCoursesChart(): array
    {
        $courses = Course::withCount('assignedUsers')
            ->orderByDesc('assigned_users_count')
            ->take(6)
            ->get();

        return [
            'labels' => $courses->pluck('title')->all(),
            'data' => $courses->pluck('assigned_users_count')->all(),
        ];
    }

    private function applicationStatusChart(): array
    {
        $labels = ['none' => 'None', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];

        $counts = User::where('role', 'user')
            ->select('salesperson_status', DB::raw('count(*) as c'))
            ->groupBy('salesperson_status')
            ->pluck('c', 'salesperson_status');

        return [
            'labels' => array_values($labels),
            'data' => collect($labels)->keys()->map(fn ($key) => (int) ($counts[$key] ?? 0))->all(),
        ];
    }

    private function ticketStatusChart(): array
    {
        $labels = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'waiting_for_user' => 'Waiting for User',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];

        $counts = SupportTicket::select('status', DB::raw('count(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        return [
            'labels' => array_values($labels),
            'data' => collect($labels)->keys()->map(fn ($key) => (int) ($counts[$key] ?? 0))->all(),
        ];
    }
}

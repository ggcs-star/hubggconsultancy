<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        $stats = $this->clientStats();

        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');
        $profileStatus = (string) $request->query('profile_status');
        $salespersonStatus = (string) $request->query('salesperson_status');
        $minPoints = $request->query('min_points');
        $maxPoints = $request->query('max_points');

        $clients = User::where('role', 'user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($profileStatus !== '', fn ($query) => $query->where('profile_completed', $profileStatus === 'complete'))
            ->when($salespersonStatus !== '', fn ($query) => $query->where('salesperson_status', $salespersonStatus))
            ->latest()
            ->get()
            ->map(function (User $client) use ($scorer) {
                $client->points = $client->lmsPoints();
                $client->assessmentScore = $scorer->score($client);

                return $client;
            });

        if ($minPoints !== null && $minPoints !== '') {
            $clients = $clients->filter(fn (User $client) => $client->points->earned >= (int) $minPoints);
        }

        if ($maxPoints !== null && $maxPoints !== '') {
            $clients = $clients->filter(fn (User $client) => $client->points->earned <= (int) $maxPoints);
        }

        $clients = $clients->values();

        $perPage = 10;
        $page = $request->integer('page', 1);

        $paginated = new LengthAwarePaginator(
            $clients->forPage($page, $perPage)->values(),
            $clients->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.clients', ['clients' => $paginated, 'stats' => $stats]);
    }

    private function clientStats(): array
    {
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('status', 'active')->count();
        $completedProfiles = User::where('role', 'user')->where('profile_completed', true)->count();

        $totalEarned = (int) DB::table('client_quiz_answers')
            ->join('users', 'users.id', '=', 'client_quiz_answers.user_id')
            ->where('users.role', 'user')
            ->whereNotNull('client_quiz_answers.points_awarded')
            ->sum('client_quiz_answers.points_awarded');

        $avgPoints = $totalUsers > 0 ? round($totalEarned / $totalUsers, 1) : 0;

        $start = now()->subDays(6)->startOfDay();
        $counts = User::where('role', 'user')
            ->where('created_at', '>=', $start)
            ->get()
            ->groupBy(fn ($user) => $user->created_at->format('Y-m-d'))
            ->map->count();

        $signupsTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $key = now()->subDays($i)->format('Y-m-d');
            $signupsTrend[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'completed_profiles' => $completedProfiles,
            'avg_points' => $avgPoints,
            'signups_trend' => $signupsTrend,
        ];
    }

    public function show(User $client, OnboardingAssessmentScorer $scorer): View
    {
        $client->load('interests', 'assignedCourses');

        return view('admin.clients-show', [
            'client' => $client,
            'points' => $client->lmsPoints(),
            'assessmentScore' => $scorer->score($client),
        ]);
    }

    public function update(Request $request, User $client): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($client->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $client->update($data);

        return back()->with('status', "{$client->name}'s details were updated.");
    }

    public function updateStatus(Request $request, User $client): RedirectResponse
    {
        abort_if($client->isAdmin(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:active,blocked,inactive'],
        ]);

        $client->update($data);

        return back()->with('status', "{$client->name}'s status is now {$data['status']}.");
    }

    public function destroy(User $client): RedirectResponse
    {
        abort_if($client->isAdmin(), 403);
        abort_if($client->id === auth()->id(), 403);

        $client->delete();

        return back()->with('status', "{$client->name} was deleted.");
    }
}

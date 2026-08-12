<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
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
            ->map(function (User $client) {
                $client->points = $client->lmsPoints();

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

        return view('admin.clients', ['clients' => $paginated]);
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestAchievement;
use App\Models\ContestPointRule;
use App\Models\ContestTargetType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContestController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $contests = Contest::query()
            ->with('targetType')
            ->withCount('registrations')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('starts_at')
            ->get();

        $contests->each(fn (Contest $contest) => $contest->finalizeIfEnded());

        if ($status !== '') {
            $contests = $contests->filter(fn (Contest $contest) => $contest->displayStatus() === $status)->values();
        }

        $perPage = 15;
        $page = Paginator::resolveCurrentPage('page');

        $paginatedContests = new LengthAwarePaginator(
            $contests->forPage($page, $perPage)->values(),
            $contests->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.contests.index', [
            'contests' => $paginatedContests,
            'eligibleCount' => User::where('role', 'user')->where('salesperson_status', 'approved')->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.contests.create', [
            'contest' => null,
            'eligibleUsers' => $this->eligibleUsers(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateContest($request);
        $selectedUserIds = $data['selected_user_ids'] ?? [];
        $pointRules = $data['point_rules'] ?? [];
        unset($data['selected_user_ids'], $data['point_rules']);

        $data['is_active'] = $request->input('submit_action') === 'publish';
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $contest = Contest::create($data);

        if ($contest->participant_mode === 'selected' && $selectedUserIds) {
            $contest->participants()->syncWithoutDetaching($selectedUserIds);
        }

        if ($contest->achievement_source === 'crm') {
            $this->syncPointRules($contest, $pointRules);
        }

        return redirect()->route('admin.contests.index')->with('status', 'Contest added.');
    }

    public function edit(Contest $contest): View
    {
        return view('admin.contests.edit', [
            'contest' => $contest,
            'eligibleUsers' => $this->eligibleUsers(),
        ]);
    }

    public function update(Request $request, Contest $contest): RedirectResponse
    {
        $data = $this->validateContest($request);
        $selectedUserIds = $data['selected_user_ids'] ?? [];
        $pointRules = $data['point_rules'] ?? [];
        unset($data['selected_user_ids'], $data['point_rules']);

        if ($request->has('submit_action')) {
            $data['is_active'] = $request->input('submit_action') === 'publish';
        }

        $data['updated_by'] = auth()->id();

        $contest->update($data);

        if ($contest->participant_mode === 'selected') {
            $contest->participants()->sync($selectedUserIds);
        }

        if ($contest->achievement_source === 'crm') {
            $this->syncPointRules($contest, $pointRules);
        } else {
            $contest->pointRules()->delete();
        }

        return redirect()->route('admin.contests.index')->with('status', 'Contest updated.');
    }

    private function syncPointRules(Contest $contest, array $pointRules): void
    {
        $contest->pointRules()->delete();

        foreach ($pointRules as $leadStatus => $points) {
            $points = (int) $points;

            if ($points <= 0 || ! array_key_exists($leadStatus, Lead::statusLabels())) {
                continue;
            }

            ContestPointRule::create([
                'contest_id' => $contest->id,
                'lead_status' => $leadStatus,
                'points' => $points,
            ]);
        }
    }

    public function destroy(Contest $contest): RedirectResponse
    {
        $contest->delete();

        return redirect()->route('admin.contests.index')->with('status', 'Contest deleted.');
    }

    public function toggleActive(Request $request, Contest $contest): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);

        $contest->update($data + ['updated_by' => auth()->id()]);

        return back()->with('status', $contest->is_active ? "\"{$contest->name}\" activated." : "\"{$contest->name}\" set to draft.");
    }

    public function participants(Contest $contest): View
    {
        $contest->finalizeIfEnded();

        $participants = $contest->participants()->orderBy('contest_participants.created_at', 'desc')->get();
        $achievements = $contest->achievements()->with(['user', 'creator', 'lead'])->latest()->get();

        return view('admin.contests.participants', [
            'contest' => $contest,
            'participants' => $participants,
            'achievements' => $achievements,
            'ranked' => $contest->rankedParticipants(),
        ]);
    }

    public function storeAchievement(Request $request, Contest $contest): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        abort_unless($contest->participants()->where('users.id', $data['user_id'])->exists(), 422, 'That user is not a participant in this contest.');
        abort_if($contest->hasEnded(), 422, 'This contest has ended — no more achievements can be logged.');
        abort_if($contest->isCrmDriven(), 422, 'This contest awards points automatically from CRM activity — manual logging is disabled.');

        ContestAchievement::create([
            'contest_id' => $contest->id,
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.contests.participants', $contest)->with('status', 'Achievement logged.');
    }

    public function destroyAchievement(ContestAchievement $achievement): RedirectResponse
    {
        $contestId = $achievement->contest_id;
        $achievement->delete();

        return redirect()->route('admin.contests.participants', $contestId)->with('status', 'Achievement entry removed.');
    }

    public function storeTargetType(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:contest_target_types,name'],
        ]);

        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $suffix = 1;

        while (ContestTargetType::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        $targetType = ContestTargetType::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $targetType->id, 'name' => $targetType->name]);
        }

        return back()->with('status', 'Target type added.');
    }

    private function eligibleUsers()
    {
        return User::where('role', 'user')->where('salesperson_status', 'approved')->orderBy('name')->get();
    }

    private function validateContest(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_type_id' => ['required', 'exists:contest_target_types,id'],
            'target' => ['nullable', 'string', 'max:255'],
            'target_value' => ['required', 'numeric', 'min:0.01'],
            'participation_type' => ['required', 'in:individual,team'],
            'participant_mode' => ['required', 'in:open,selected'],
            'selected_user_ids' => ['nullable', 'array'],
            'selected_user_ids.*' => ['exists:users,id'],
            'achievement_source' => ['required', 'in:manual,crm'],
            'point_rules' => ['nullable', 'array'],
            'point_rules.*' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'reward' => ['nullable', 'string', 'max:255'],
            'reward_type' => ['nullable', 'in:points,bonus,cash,gift'],
            'reward_second' => ['nullable', 'string', 'max:255'],
            'reward_third' => ['nullable', 'string', 'max:255'],
            'min_achievement' => ['nullable', 'numeric', 'min:0'],
            'counting_method' => ['nullable', 'string', 'max:255'],
            'tie_breaker' => ['nullable', 'string', 'max:255'],
            'eligibility' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        return $data;
    }
}

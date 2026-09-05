<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingChecklistCompletion;
use App\Models\OnboardingChecklistItem;
use App\Models\User;
use App\Services\TeamApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct(private TeamApiService $teamApi)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->gg_user_id) {
            return redirect()->route('user.profile')
                ->with('status', 'Add your GG User ID in your profile to view your team.');
        }

        $tree = $this->teamApi->tree($user->gg_user_id, 6);

        if (! $tree) {
            return view('user.team.index', ['apiError' => true]);
        }

        $purchases = $this->teamApi->purchases($user->gg_user_id);
        $rootPurchase = $purchases['purchases'][0] ?? null;

        $rootNode = $tree['tree'][0] ?? null;

        $members = [];
        $this->flatten($rootNode['children'] ?? [], $members);

        $ggIds = collect($members)->pluck('user_id')->filter()->map(fn ($id) => (string) $id)->unique()->values();
        $localUsers = User::whereIn('gg_user_id', $ggIds)->get()->keyBy(fn (User $u) => (string) $u->gg_user_id);

        $checklistItems = OnboardingChecklistItem::published()->ordered()->get(['id', 'title']);
        $relevantUserIds = $localUsers->pluck('id')->push($user->id);
        $completionsByUser = OnboardingChecklistCompletion::whereIn('user_id', $relevantUserIds)
            ->get()
            ->groupBy('user_id');

        $rows = collect($members)->map(function (array $member) use ($localUsers, $checklistItems, $completionsByUser) {
            $localUser = $localUsers->get((string) $member['user_id']);

            $checklist = $this->checklistStatus($localUser, $checklistItems, $completionsByUser);

            return (object) [
                'user_id' => $member['user_id'],
                'level' => $member['level'],
                'purchase_code' => $member['purchase_code'],
                'name' => $member['name'],
                'username' => $member['username'],
                'joined_at' => $member['joined_at'],
                'on_platform' => $localUser !== null,
                'checklist' => $checklist,
                'checklist_complete' => $checklistItems->isNotEmpty() && $checklist->every(fn ($item) => $item->completed),
                'kyc_verified' => $this->kycVerified($member['user_id'] ? (string) $member['user_id'] : null),
            ];
        })->values();

        $progressByUserId = $rows->filter(fn ($row) => $row->user_id !== null)
            ->keyBy(fn ($row) => (string) $row->user_id)
            ->map(fn ($row) => (object) [
                'on_platform' => $row->on_platform,
                'checklist' => $row->checklist,
                'kyc_verified' => $row->kyc_verified,
            ]);

        $ownChecklist = $this->checklistStatus($user, $checklistItems, $completionsByUser);
        $ownKycVerified = $this->kycVerified($user->gg_user_id);

        // Cached so the lazy "expand a branch" AJAX endpoint (children()) can
        // serve already-fetched nodes/progress instantly instead of hitting
        // the external Team API again for every click — only branches beyond
        // this fetch's depth (or a cold cache) fall back to a fresh call.
        $nodesByUserId = [];
        $this->collectNodesByUserId($rootNode ? [$rootNode] : [], $nodesByUserId);
        Cache::put("gg_team_nodes_{$user->id}", $nodesByUserId, now()->addMinutes(20));
        Cache::put("gg_team_progress_{$user->id}", $progressByUserId, now()->addMinutes(20));

        $totalMembers = $tree['total_team'] ?? $rows->count();
        $purchasedCount = $rows->filter(fn ($row) => filled($row->purchase_code))->count();
        $onboardingCompleteCount = $rows->filter(fn ($row) => $row->checklist_complete)->count();

        $memberRows = $this->paginateMembers($request, $rows);

        return view('user.team.index', [
            'apiError' => false,
            'user' => $user,
            'truncated' => $tree['truncated'] ?? false,
            'rootNode' => $rootNode,
            'rootPurchase' => $rootPurchase,
            'checklistItems' => $checklistItems,
            'ownChecklist' => $ownChecklist,
            'ownKycVerified' => $ownKycVerified,
            'progressByUserId' => $progressByUserId,
            'rows' => $rows,
            'memberRows' => $memberRows,
            'stats' => [
                'total_members' => $totalMembers,
                'purchased_count' => $purchasedCount,
                'purchased_percent' => $totalMembers > 0 ? (int) round($purchasedCount / $totalMembers * 100) : 0,
                'onboarding_complete_count' => $onboardingCompleteCount,
                'onboarding_complete_percent' => $totalMembers > 0 ? (int) round($onboardingCompleteCount / $totalMembers * 100) : 0,
            ],
        ]);
    }

    /**
     * Applies the Members tab's search/level filter server-side and slices
     * the result into real pages of 10, so the "Showing X of Y" + page
     * number controls (shown for every other list in this app, even with
     * only a handful of results) also appear here instead of the table
     * silently rendering everyone at once.
     */
    private function paginateMembers(Request $request, Collection $rows): LengthAwarePaginator
    {
        $search = trim((string) $request->query('search', ''));
        $level = trim((string) $request->query('level', ''));

        $filtered = $rows->filter(function ($row) use ($search, $level) {
            $matchesSearch = $search === '' || str_contains(Str::lower($row->name ?? ''), Str::lower($search));
            $matchesLevel = $level === '' || (string) $row->level === $level;

            return $matchesSearch && $matchesLevel;
        })->values();

        $perPage = 10;
        $page = Paginator::resolveCurrentPage('page');

        return new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );
    }

    /**
     * Lazily serves one branch of the tree (called via AJAX when a node is
     * expanded on the My Team page). Only ever returns children of a node
     * that was already part of THIS user's own cached tree() fetch from
     * index() — never an arbitrary id the client sends, since that would let
     * one user pull another user's downline just by guessing/passing their
     * gg_user_id. A cache miss (id not in it, or the cache expired) is
     * refused rather than "helpfully" falling back to a fresh unscoped API
     * call; the client asks the user to reload the page, which repopulates
     * the cache from their own tree.
     */
    public function children(Request $request, string $ggUserId): View
    {
        $user = $request->user();

        $nodesByUserId = Cache::get("gg_team_nodes_{$user->id}", []);
        $node = $nodesByUserId[$ggUserId] ?? null;

        abort_if(! $node, 404);

        $progressByUserId = Cache::get("gg_team_progress_{$user->id}", collect());
        $colorIndex = (int) $request->query('color', 0);

        return view('partials.team-tree-branch', [
            'children' => $node['children'] ?? [],
            'colorIndex' => $colorIndex,
            'progressByUserId' => $progressByUserId,
        ]);
    }

    private function collectNodesByUserId(array $nodes, array &$out): void
    {
        foreach ($nodes as $node) {
            $userId = isset($node['user']['user_id']) ? (string) $node['user']['user_id'] : null;

            if ($userId) {
                $out[$userId] = $node;
            }

            if (! empty($node['children'])) {
                $this->collectNodesByUserId($node['children'], $out);
            }
        }
    }

    private function flatten(array $nodes, array &$out): void
    {
        foreach ($nodes as $node) {
            $out[] = [
                'level' => $node['level'] ?? null,
                'purchase_code' => $node['purchase_code'] ?? null,
                'user_id' => $node['user']['user_id'] ?? null,
                'username' => $node['user']['username'] ?? null,
                'name' => $node['user']['name'] ?? null,
                'joined_at' => $node['user']['joined_at'] ?? null,
            ];

            if (! empty($node['children'])) {
                $this->flatten($node['children'], $out);
            }
        }
    }

    /**
     * KYC verification status as reported by GG Prime's /member/profile
     * endpoint, cached briefly per GG user id since the tree/members list
     * would otherwise fire one API call per team member on every load.
     * Returns null (rendered as "—") when the member isn't linked to a GG
     * Prime account or the API can't be reached.
     */
    private function kycVerified(?string $ggUserId): ?bool
    {
        if (! $ggUserId) {
            return null;
        }

        return Cache::remember("gg_kyc_verified_{$ggUserId}", now()->addMinutes(30), function () use ($ggUserId) {
            $result = $this->teamApi->profile(['user_id' => $ggUserId]);

            return $result->status === 'found' ? (bool) ($result->data['kyc_verified'] ?? false) : null;
        });
    }

    /**
     * Per-item completion status for one user against the admin-configured
     * onboarding checklist (whatever items exist there) — this is what
     * drives the tick/light indicators on the My Team tree and members list.
     *
     * @return Collection<int, object{id: int, title: string, completed: bool}>
     */
    private function checklistStatus(?User $user, Collection $checklistItems, Collection $completionsByUser): Collection
    {
        $completedItemIds = $user
            ? $completionsByUser->get($user->id, collect())->pluck('onboarding_checklist_item_id')->all()
            : [];

        return $checklistItems->map(fn (OnboardingChecklistItem $item) => (object) [
            'id' => $item->id,
            'title' => $item->title,
            'completed' => in_array($item->id, $completedItemIds, true),
        ])->values();
    }
}

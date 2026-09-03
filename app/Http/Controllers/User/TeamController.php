<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingChecklistCompletion;
use App\Models\OnboardingChecklistItem;
use App\Models\User;
use App\Services\TeamApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            ];
        })->values();

        $progressByUserId = $rows->filter(fn ($row) => $row->user_id !== null)
            ->keyBy(fn ($row) => (string) $row->user_id)
            ->map(fn ($row) => (object) [
                'on_platform' => $row->on_platform,
                'checklist' => $row->checklist,
            ]);

        $ownChecklist = $this->checklistStatus($user, $checklistItems, $completionsByUser);

        $totalMembers = $tree['total_team'] ?? $rows->count();
        $purchasedCount = $rows->filter(fn ($row) => filled($row->purchase_code))->count();
        $onboardingCompleteCount = $rows->filter(fn ($row) => $row->checklist_complete)->count();

        return view('user.team.index', [
            'apiError' => false,
            'user' => $user,
            'truncated' => $tree['truncated'] ?? false,
            'rootNode' => $rootNode,
            'rootPurchase' => $rootPurchase,
            'checklistItems' => $checklistItems,
            'ownChecklist' => $ownChecklist,
            'progressByUserId' => $progressByUserId,
            'rows' => $rows,
            'stats' => [
                'total_members' => $totalMembers,
                'purchased_count' => $purchasedCount,
                'purchased_percent' => $totalMembers > 0 ? (int) round($purchasedCount / $totalMembers * 100) : 0,
                'onboarding_complete_count' => $onboardingCompleteCount,
                'onboarding_complete_percent' => $totalMembers > 0 ? (int) round($onboardingCompleteCount / $totalMembers * 100) : 0,
            ],
        ]);
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

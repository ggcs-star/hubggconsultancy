<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingChecklistCompletion;
use App\Models\OnboardingChecklistItem;
use App\Models\TeamMember;
use App\Models\TeamMemberChange;
use App\Models\User;
use App\Services\TeamApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

        // Only level 1 is fetched here — anything deeper is fetched + synced
        // lazily by children() the first time it's actually clicked open.
        // Fetching the full downline up front used to mean one external KYC
        // lookup per member, made sequentially; with a real team that meant
        // several 10-second timeouts stacking up on a single page load. This
        // keeps the first load fast and bounded, at the cost of the Members
        // tab / stats below only reflecting what's been explored so far —
        // see the "as discovered" note passed to the view.
        $tree = $this->teamApi->tree($user->gg_user_id, 1);
        $stale = false;
        $lastSyncedAt = null;

        if (! $tree) {
            // The live API is unreachable — fall back to whatever we last
            // synced to team_members instead of showing an error page. If
            // we've never successfully synced this user before, there's
            // nothing to fall back to, so it's still a hard error.
            $tree = $this->buildTreeFromStorage($user);

            if (! $tree) {
                return view('user.team.index', ['apiError' => true]);
            }

            $stale = true;
            $lastSyncedAt = TeamMember::where('owner_user_id', $user->id)->max('last_synced_at');
        }

        $purchases = $stale ? null : $this->teamApi->purchases($user->gg_user_id);
        $rootPurchase = $purchases['purchases'][0] ?? null;

        $rootNode = $tree['tree'][0] ?? null;

        if (! $stale) {
            // Only level 1 comes back from this shallow fetch. Sync just
            // that — scoped so this doesn't wipe out deeper branches already
            // synced from earlier clicks into the Tree tab (see
            // syncTeamMembers()'s $exhaustiveForParentIds).
            $level1Members = [];
            $this->flatten($rootNode['children'] ?? [], $level1Members, $user->gg_user_id);
            $this->syncTeamMembers($user, $level1Members, exhaustiveForParentIds: [$user->gg_user_id]);
        }

        // The Members tab and stats are built from whatever's actually
        // stored for this user so far — level 1 right after a fresh load,
        // growing as deeper branches get opened via the Tree tab. This is
        // deliberately "as discovered", not a guaranteed complete snapshot.
        $stored = TeamMember::where('owner_user_id', $user->id)->get();

        $ggIds = $stored->pluck('gg_user_id')->unique()->values();
        $localUsers = User::whereIn('gg_user_id', $ggIds)->get()->keyBy(fn (User $u) => (string) $u->gg_user_id);

        $checklistItems = OnboardingChecklistItem::published()->ordered()->get(['id', 'title']);
        $relevantUserIds = $localUsers->pluck('id')->push($user->id);
        $completionsByUser = OnboardingChecklistCompletion::whereIn('user_id', $relevantUserIds)
            ->get()
            ->groupBy('user_id');

        $rows = $stored->map(function (TeamMember $member) use ($localUsers, $checklistItems, $completionsByUser) {
            $localUser = $localUsers->get($member->gg_user_id);

            $checklist = $this->checklistStatus($localUser, $checklistItems, $completionsByUser);

            return (object) [
                'user_id' => $member->gg_user_id,
                'level' => $member->level,
                'purchase_code' => $member->purchase_code,
                'name' => $member->name,
                'username' => $member->username,
                'joined_at' => optional($member->joined_at)->toDateTimeString(),
                'on_platform' => $localUser !== null,
                'checklist' => $checklist,
                'checklist_complete' => $checklistItems->isNotEmpty() && $checklist->every(fn ($item) => $item->completed),
                'kyc_verified' => $member->kyc_verified,
                'team_size' => $member->team_size,
            ];
        })->values();

        $progressByUserId = $rows->keyBy(fn ($row) => (string) $row->user_id)
            ->map(fn ($row) => (object) [
                'on_platform' => $row->on_platform,
                'checklist' => $row->checklist,
                'kyc_verified' => $row->kyc_verified,
                'team_size' => $row->team_size,
            ]);

        $ownChecklist = $this->checklistStatus($user, $checklistItems, $completionsByUser);

        // Persisted on the user record itself (not just used here) so pages
        // outside My Team — the header avatar, for instance — can show
        // verified/unverified status without a live API call on every
        // request. Falls back to whatever was last stored when the API
        // can't be reached right now, rather than showing nothing.
        if ($stale) {
            $ownKycVerified = $user->kyc_verified;
            // No live call while stale — Level 1 (always fully synced) is
            // the closest available approximation of GG Prime's own count.
            $directReferralCount = $rows->where('level', 1)->count();
        } else {
            $ownProfileStats = $this->profileStats($user->gg_user_id);
            $ownKycVerified = $ownProfileStats['kyc_verified'];
            // Same source as the Profile page's "Direct Referrals" figure —
            // GG Prime's own /member/profile count, not a local recount from
            // the tree, so the two pages can never show different numbers.
            $directReferralCount = $ownProfileStats['direct_referrals'] ?? $rows->where('level', 1)->count();

            if (! is_null($ownKycVerified) && $user->kyc_verified !== $ownKycVerified) {
                $user->update(['kyc_verified' => $ownKycVerified]);
            }
        }

        // Level 1 is always shown immediately (not behind a click), so it
        // needs fresh stats up front — but that's at most however many
        // direct referrals you have, not one call per member in the team.
        if (! $stale) {
            foreach ($rootNode['children'] ?? [] as $level1Node) {
                $level1Id = isset($level1Node['user']['user_id']) ? (string) $level1Node['user']['user_id'] : null;

                if (! $level1Id || ! $progressByUserId->has($level1Id)) {
                    continue;
                }

                $stats = $this->profileStats($level1Id);
                $progressByUserId[$level1Id]->kyc_verified = $stats['kyc_verified'];
                $progressByUserId[$level1Id]->team_size = $stats['team_size'];
                $this->syncProfileStats($user, $level1Id, $stats);
            }
        }

        $memberRows = $this->paginateMembers($request, $rows);

        // Same idea for the Members table: only the page of rows actually on
        // screen gets checked, not the whole (potentially huge) stored set.
        if (! $stale) {
            foreach ($memberRows as $row) {
                if (! $row->user_id || ! is_null($row->kyc_verified)) {
                    continue;
                }

                $rowId = (string) $row->user_id;
                $stats = $this->profileStats($rowId);
                $row->kyc_verified = $stats['kyc_verified'];
                $row->team_size = $stats['team_size'];
                $this->syncProfileStats($user, $rowId, $stats);

                if ($progressByUserId->has($rowId)) {
                    $progressByUserId[$rowId]->kyc_verified = $row->kyc_verified;
                    $progressByUserId[$rowId]->team_size = $row->team_size;
                }
            }
        }

        // Cached so the lazy "expand a branch" AJAX endpoint (children()) can
        // serve already-fetched nodes/progress instantly instead of hitting
        // the external Team API again for every click — only branches beyond
        // level 1 (or a cold cache) fall back to a fresh/stored lookup.
        $nodesByUserId = [];
        $this->collectNodesByUserId($rootNode ? [$rootNode] : [], $nodesByUserId);
        Cache::put("gg_team_nodes_{$user->id}", $nodesByUserId, now()->addMinutes(20));
        Cache::put("gg_team_progress_{$user->id}", $progressByUserId, now()->addMinutes(20));

        // total_team is GG Prime's own aggregate for your whole downline —
        // it stays accurate even from this shallow fetch. Purchased/
        // Onboarding counts, though, can only reflect what's actually been
        // synced to team_members so far ($discoveredCount of $totalMembers).
        $totalMembers = $tree['total_team'] ?? $rows->count();
        $discoveredCount = $rows->count();
        $onboardingCompleteCount = $rows->filter(fn ($row) => $row->checklist_complete)->count();

        return view('user.team.index', [
            'apiError' => false,
            'stale' => $stale,
            'lastSyncedAt' => $lastSyncedAt,
            'partial' => $discoveredCount < $totalMembers,
            'user' => $user,
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
                'discovered_count' => $discoveredCount,
                'direct_referral_count' => $directReferralCount,
                'onboarding_complete_count' => $onboardingCompleteCount,
                'onboarding_complete_percent' => $discoveredCount > 0 ? (int) round($onboardingCompleteCount / $discoveredCount * 100) : 0,
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
     * expanded on the My Team page). Resolution order:
     *
     *   1. The 20-minute node cache from index() — fast path, no DB or API.
     *   2. team_members — if the cache expired, this confirms $ggUserId is
     *      genuinely part of THIS user's own synced downline (it can only be
     *      in there because syncTeamMembers() put it there from this same
     *      user's own tree() fetch). Unlike the cache, this never expires.
     *   3. Once ownership is confirmed via step 2, it's now safe to ask GG
     *      Prime for a fresh look at just this branch — safe because we're
     *      no longer trusting an unverified client-supplied id, only an id
     *      already proven to belong to this user. A successful live result
     *      is mirrored back into team_members (so a brand new member shows
     *      up here too); a failed one just uses the already-verified stored
     *      node instead.
     *
     * An id that fails step 2 is refused outright (404) — it was never part
     * of this user's own team by any record we have, live or stored.
     */
    public function children(Request $request, string $ggUserId): View
    {
        $user = $request->user();

        $nodesByUserId = Cache::get("gg_team_nodes_{$user->id}", []);
        $node = $nodesByUserId[$ggUserId] ?? null;
        $progressByUserId = Cache::get("gg_team_progress_{$user->id}") ?? collect();

        // A node can be present in the cache with an empty 'children' array
        // for two different reasons: it genuinely has no downline, or — for
        // every level 1 member, always — its children were simply never
        // fetched (index() only fetches level 1 itself; everyone's own
        // children are lazy). team_size (from their profile stats, already
        // known for level 1) tells these apart without needing a fetch just
        // to find out: if it's >0, or the node isn't cached at all, this
        // click needs to go get the real children.
        $knownSize = $progressByUserId[$ggUserId]->team_size ?? null;
        $needsFetch = empty($node['children'] ?? []) && (! $node || $knownSize === null || $knownSize > 0);

        if ($needsFetch) {
            // If this id isn't already trusted via the node cache (i.e. it's
            // not even a known level-1 member), team_members must first
            // confirm it genuinely belongs to this user's own downline
            // before doing anything else with it — same security boundary
            // as always, just skipped when the cache already establishes
            // that trust.
            $storedNode = $node ? null : $this->findNodeFromStorage($user, $ggUserId);

            abort_if(! $node && ! $storedNode, 404);

            $fresh = $this->teamApi->tree($ggUserId, 1);
            $freshNode = $fresh['tree'][0] ?? null;

            if ($freshNode) {
                $node = $freshNode;

                $freshMembers = [];
                $this->flatten($node['children'] ?? [], $freshMembers, $ggUserId);
                $this->syncTeamMembers($user, $freshMembers, exhaustiveForParentIds: [$ggUserId]);
            } elseif ($storedNode) {
                $node = $storedNode;
            } else {
                // Live call failed and this wasn't a cold-cache lookup (so no
                // DB check ran above) — fall back to whatever's stored for
                // this specific id now, same as the cold-cache path would.
                $node = $this->findNodeFromStorage($user, $ggUserId) ?? $node;
            }

            // Newly revealed children won't be in the cached progress map —
            // on_platform/checklist aren't in team_members, so they need
            // rebuilding fresh from local tables rather than left blank.
            $freshProgress = $this->buildProgressForNodes($node['children'] ?? []);
            $progressByUserId = $progressByUserId->isEmpty() ? $freshProgress : $progressByUserId->union($freshProgress);
        }

        $colorIndex = (int) $request->query('color', 0);

        // Profile stats (KYC + this member's own downline size) are
        // deliberately not precomputed for the whole tree (see index()) —
        // fill them in now just for the handful of children this one click
        // is about to reveal.
        foreach ($node['children'] ?? [] as $child) {
            $childId = isset($child['user']['user_id']) ? (string) $child['user']['user_id'] : null;

            if (! $childId) {
                continue;
            }

            if (! $progressByUserId->has($childId)) {
                $progressByUserId[$childId] = (object) ['on_platform' => false, 'checklist' => collect(), 'kyc_verified' => null, 'team_size' => null];
            }

            if (is_null($progressByUserId[$childId]->kyc_verified)) {
                $stats = $this->profileStats($childId);
                $progressByUserId[$childId]->kyc_verified = $stats['kyc_verified'];
                $progressByUserId[$childId]->team_size = $stats['team_size'];
                $this->syncProfileStats($user, $childId, $stats);
            }
        }

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

    private function flatten(array $nodes, array &$out, ?string $parentGgUserId = null): void
    {
        foreach ($nodes as $node) {
            $ggUserId = isset($node['user']['user_id']) ? (string) $node['user']['user_id'] : null;

            $out[] = [
                'level' => $node['level'] ?? null,
                'purchase_code' => $node['purchase_code'] ?? null,
                'user_id' => $node['user']['user_id'] ?? null,
                'username' => $node['user']['username'] ?? null,
                'name' => $node['user']['name'] ?? null,
                'joined_at' => $node['user']['joined_at'] ?? null,
                'parent_gg_user_id' => $parentGgUserId,
            ];

            if (! empty($node['children'])) {
                $this->flatten($node['children'], $out, $ggUserId);
            }
        }
    }

    /**
     * Mirrors one successful tree()/branch fetch into team_members, so an
     * API outage has real, recent data to fall back to instead of an error
     * page. Every trackable field is diffed against what was stored before
     * and any change — including a brand new member showing up, or an
     * existing one disappearing — is logged to team_member_changes, a full
     * audit trail rather than just a "last updated" timestamp.
     *
     * Deliberately does a small, fixed number of queries (one read, one
     * bulk upsert, one bulk change-log insert) no matter how many members
     * are in $members — a naive per-member firstOrNew()+save() loop would
     * mean ~2000 queries for a 1000-person team, which defeats the entire
     * point of caching this data in the first place.
     *
     * $members isn't always the whole downline — children() also calls this
     * with just one freshly re-fetched branch, and index() now only ever
     * passes level 1. $exhaustiveForParentIds says which parents' children
     * $members exhaustively lists, so "removed" detection only ever compares
     * like for like: passing null means $members is the entire team (not
     * used currently, since every caller now syncs a bounded slice), while
     * an explicit list means only those specific parents' previously known
     * children are checked for removal — never the rest of the team, which
     * this call knows nothing about and must leave untouched.
     */
    private function syncTeamMembers(User $user, array $members, ?array $exhaustiveForParentIds = null): void
    {
        $now = now();

        $ggIdsBeingSynced = collect($members)->pluck('user_id')->filter()->map(fn ($id) => (string) $id)->values();

        $existingByGgId = TeamMember::where('owner_user_id', $user->id)
            ->whereIn('gg_user_id', $ggIdsBeingSynced)
            ->get()
            ->keyBy('gg_user_id');

        $trackedFields = ['parent_gg_user_id', 'level', 'name', 'username', 'purchase_code', 'joined_at'];
        $upsertRows = [];
        $changeLog = []; // keyed by gg_user_id => [ [field, old, new], ... ], applied after we know each row's id
        $seenGgIds = [];

        foreach ($members as $member) {
            if (! $member['user_id']) {
                continue;
            }

            $ggUserId = (string) $member['user_id'];
            $seenGgIds[] = $ggUserId;

            $existing = $existingByGgId->get($ggUserId);
            $joinedAt = $member['joined_at'] ? Carbon::parse($member['joined_at']) : null;

            $newValues = [
                'parent_gg_user_id' => $member['parent_gg_user_id'],
                'level' => $member['level'],
                'name' => $member['name'],
                'username' => $member['username'],
                'purchase_code' => $member['purchase_code'],
                'joined_at' => $joinedAt,
            ];

            if ($existing) {
                foreach ($trackedFields as $field) {
                    $old = $existing->{$field};
                    $oldComparable = $old instanceof Carbon ? $old->toDateTimeString() : $old;
                    $newComparable = $newValues[$field] instanceof Carbon ? $newValues[$field]->toDateTimeString() : $newValues[$field];

                    if ((string) $oldComparable !== (string) $newComparable) {
                        $changeLog[$ggUserId][] = [
                            'field' => $field,
                            'old_value' => $oldComparable !== null ? (string) $oldComparable : null,
                            'new_value' => $newComparable !== null ? (string) $newComparable : null,
                        ];
                    }
                }
            } else {
                $changeLog[$ggUserId][] = ['field' => '_added', 'old_value' => null, 'new_value' => 'Added to team'];
            }

            $upsertRows[] = [
                'owner_user_id' => $user->id,
                'gg_user_id' => $ggUserId,
                'parent_gg_user_id' => $newValues['parent_gg_user_id'],
                'level' => $newValues['level'],
                'name' => $newValues['name'],
                'username' => $newValues['username'],
                'purchase_code' => $newValues['purchase_code'],
                'joined_at' => $joinedAt?->toDateTimeString(),
                'last_synced_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Anyone previously stored under the parent(s) this sync exhaustively
        // covers, but who didn't show up this time, has left the downline
        // (or been restructured elsewhere) — worth a change record, not just
        // silently going stale. Scoped: a full sync compares against the
        // whole team; a single-branch refresh only compares against that
        // branch's own previously known children.
        $existingForRemovalCheck = is_null($exhaustiveForParentIds)
            ? TeamMember::where('owner_user_id', $user->id)->pluck('gg_user_id')
            : TeamMember::where('owner_user_id', $user->id)->whereIn('parent_gg_user_id', $exhaustiveForParentIds)->pluck('gg_user_id');

        $removedGgIds = $existingForRemovalCheck->diff($seenGgIds);

        foreach ($removedGgIds as $ggUserId) {
            $changeLog[$ggUserId][] = ['field' => '_removed', 'old_value' => 'In team', 'new_value' => 'No longer in team'];
        }

        if (empty($upsertRows) && $removedGgIds->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($user, $upsertRows, $trackedFields, $now, $removedGgIds, $changeLog) {
            if (! empty($upsertRows)) {
                TeamMember::upsert(
                    $upsertRows,
                    ['owner_user_id', 'gg_user_id'],
                    [...$trackedFields, 'last_synced_at', 'updated_at']
                );
            }

            if ($removedGgIds->isNotEmpty()) {
                TeamMember::where('owner_user_id', $user->id)
                    ->whereIn('gg_user_id', $removedGgIds)
                    ->update(['last_synced_at' => $now]);
            }

            if (empty($changeLog)) {
                return;
            }

            $idsByGgId = TeamMember::where('owner_user_id', $user->id)
                ->whereIn('gg_user_id', array_keys($changeLog))
                ->pluck('id', 'gg_user_id');

            $changeRows = [];

            foreach ($changeLog as $ggUserId => $fieldChanges) {
                $teamMemberId = $idsByGgId->get($ggUserId);

                if (! $teamMemberId) {
                    continue;
                }

                foreach ($fieldChanges as $change) {
                    $changeRows[] = [
                        'team_member_id' => $teamMemberId,
                        'field' => $change['field'],
                        'old_value' => $change['old_value'],
                        'new_value' => $change['new_value'],
                        'changed_at' => $now,
                    ];
                }
            }

            if (! empty($changeRows)) {
                TeamMemberChange::insert($changeRows);
            }
        });
    }

    /**
     * Persists freshly fetched profile stats (KYC + this member's own
     * downline size) for one member, logging a change record for whichever
     * of those flipped since the last time we stored them. No-ops if this
     * member hasn't been synced via syncTeamMembers() yet (e.g. an id that
     * isn't actually part of this user's own team).
     *
     * @param  array{kyc_verified: ?bool, team_size: ?int}  $stats
     */
    private function syncProfileStats(User $user, string $ggUserId, array $stats): void
    {
        $teamMember = TeamMember::where('owner_user_id', $user->id)
            ->where('gg_user_id', $ggUserId)
            ->first();

        if (! $teamMember) {
            return;
        }

        $changeRows = [];
        $now = now();

        if ($teamMember->kyc_verified !== $stats['kyc_verified']) {
            $changeRows[] = [
                'team_member_id' => $teamMember->id,
                'field' => 'kyc_verified',
                'old_value' => is_null($teamMember->kyc_verified) ? null : ($teamMember->kyc_verified ? 'true' : 'false'),
                'new_value' => is_null($stats['kyc_verified']) ? null : ($stats['kyc_verified'] ? 'true' : 'false'),
                'changed_at' => $now,
            ];
        }

        if ($teamMember->team_size !== $stats['team_size']) {
            $changeRows[] = [
                'team_member_id' => $teamMember->id,
                'field' => 'team_size',
                'old_value' => $teamMember->team_size !== null ? (string) $teamMember->team_size : null,
                'new_value' => $stats['team_size'] !== null ? (string) $stats['team_size'] : null,
                'changed_at' => $now,
            ];
        }

        if (empty($changeRows)) {
            // Still bump last_synced_at so we know these stats were just
            // confirmed fresh, even though nothing actually changed.
            $teamMember->update(['last_synced_at' => $now]);

            return;
        }

        TeamMemberChange::insert($changeRows);

        $teamMember->update([
            'kyc_verified' => $stats['kyc_verified'],
            'team_size' => $stats['team_size'],
            'last_synced_at' => $now,
        ]);
    }

    /**
     * Returns a recursive closure that turns one TeamMember row into a
     * tree()-shaped node (same structure the external API returns),
     * including its own descendants — shared by buildTreeFromStorage() and
     * findNodeFromStorage() so both build nodes the exact same way.
     */
    private function storedNodeBuilder(Collection $byParent): \Closure
    {
        $buildNode = function (TeamMember $member) use (&$buildNode, $byParent): array {
            return [
                'level' => $member->level,
                'purchase_code' => $member->purchase_code,
                'user' => [
                    'user_id' => $member->gg_user_id,
                    'username' => $member->username,
                    'name' => $member->name,
                    'joined_at' => optional($member->joined_at)->toDateTimeString(),
                ],
                'children' => $byParent->get($member->gg_user_id, collect())
                    ->map($buildNode)
                    ->values()
                    ->all(),
            ];
        };

        return $buildNode;
    }

    /**
     * Rebuilds a tree() -shaped array (same structure the external API
     * returns) from whatever's stored in team_members, for when the live API
     * call in index() fails. Returns null if this user has never been
     * synced before, since there's genuinely nothing to fall back to.
     */
    private function buildTreeFromStorage(User $user): ?array
    {
        $stored = TeamMember::where('owner_user_id', $user->id)->get();

        if ($stored->isEmpty()) {
            return null;
        }

        $byParent = $stored->groupBy('parent_gg_user_id');
        $buildNode = $this->storedNodeBuilder($byParent);

        $rootNode = [
            'level' => 0,
            'purchase_code' => null,
            'user' => [
                'user_id' => $user->gg_user_id,
                'username' => $user->phone,
                'name' => $user->name,
                'joined_at' => null,
            ],
            'children' => $byParent->get($user->gg_user_id, collect())
                ->map($buildNode)
                ->values()
                ->all(),
        ];

        return [
            'tree' => [$rootNode],
            'total_team' => $stored->count(),
            'truncated' => false,
        ];
    }

    /**
     * Finds one specific node (plus its stored descendants) in this user's
     * own synced downline — used by children() when the 20-minute node
     * cache has expired. Returning a result here is also the security proof
     * that $ggUserId genuinely belongs to this user's own team (it can only
     * be in team_members because syncTeamMembers() put it there from this
     * same user's own tree() fetch) — unlike the cache, this check never
     * expires, so a click made after the cache window closes doesn't have
     * to be refused outright.
     */
    private function findNodeFromStorage(User $user, string $ggUserId): ?array
    {
        $stored = TeamMember::where('owner_user_id', $user->id)->get();

        $target = $stored->firstWhere('gg_user_id', $ggUserId);

        if (! $target) {
            return null;
        }

        $byParent = $stored->groupBy('parent_gg_user_id');

        return ($this->storedNodeBuilder($byParent))($target);
    }

    /**
     * Builds on_platform/checklist progress info for a set of raw tree
     * nodes, straight from local tables — used when children() has to
     * rebuild a branch outside of index()'s normal flow (cache expired) and
     * so doesn't already have this from the cached progress map.
     */
    private function buildProgressForNodes(array $nodes): Collection
    {
        $ggIds = collect($nodes)
            ->map(fn ($node) => isset($node['user']['user_id']) ? (string) $node['user']['user_id'] : null)
            ->filter()
            ->values();

        $localUsers = User::whereIn('gg_user_id', $ggIds)->get()->keyBy(fn (User $u) => (string) $u->gg_user_id);

        $checklistItems = OnboardingChecklistItem::published()->ordered()->get(['id', 'title']);
        $completionsByUser = OnboardingChecklistCompletion::whereIn('user_id', $localUsers->pluck('id'))
            ->get()
            ->groupBy('user_id');

        return $ggIds->mapWithKeys(function ($ggId) use ($localUsers, $checklistItems, $completionsByUser) {
            $localUser = $localUsers->get($ggId);

            return [$ggId => (object) [
                'on_platform' => $localUser !== null,
                'checklist' => $this->checklistStatus($localUser, $checklistItems, $completionsByUser),
                'kyc_verified' => null,
                'team_size' => null,
            ]];
        });
    }

    /**
     * KYC verification status + this member's own downline size, both from
     * GG Prime's /member/profile endpoint in a single call, cached together
     * per GG user id — the tree/members list would otherwise fire one API
     * call per team member on every load. Both come back null (rendered as
     * "—" / treated as "no known children") when the member isn't linked to
     * a GG Prime account or the API can't be reached.
     *
     * @return array{kyc_verified: ?bool, team_size: ?int}
     */
    private function profileStats(?string $ggUserId): array
    {
        if (! $ggUserId) {
            return ['kyc_verified' => null, 'team_size' => null, 'direct_referrals' => null];
        }

        return Cache::remember("gg_profile_stats_{$ggUserId}", now()->addMinutes(30), function () use ($ggUserId) {
            $result = $this->teamApi->profile(['user_id' => $ggUserId]);

            if ($result->status !== 'found') {
                return ['kyc_verified' => null, 'team_size' => null, 'direct_referrals' => null];
            }

            return [
                'kyc_verified' => (bool) ($result->data['kyc_verified'] ?? false),
                'team_size' => isset($result->data['total_team']) ? (int) $result->data['total_team'] : null,
                'direct_referrals' => isset($result->data['direct_referrals']) ? (int) $result->data['direct_referrals'] : null,
            ];
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

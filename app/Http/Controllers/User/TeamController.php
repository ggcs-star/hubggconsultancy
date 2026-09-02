<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ContentView;
use App\Models\Document;
use App\Models\SalesManual;
use App\Models\SalesToolkitItem;
use App\Models\ScriptItem;
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

        [$publishedContentIds, $totalContentCount] = $this->publishedContentIds();
        $viewsByUser = ContentView::whereIn('user_id', $localUsers->pluck('id'))->get()->groupBy('user_id');

        $rows = collect($members)->map(function (array $member) use ($localUsers, $publishedContentIds, $totalContentCount, $viewsByUser) {
            $localUser = $localUsers->get((string) $member['user_id']);

            $video = $localUser ? $this->videoProgress($localUser) : null;
            $documents = $localUser
                ? $this->documentProgress($publishedContentIds, $totalContentCount, $viewsByUser->get($localUser->id, collect()))
                : null;

            return (object) [
                'level' => $member['level'],
                'purchase_code' => $member['purchase_code'],
                'name' => $member['name'],
                'username' => $member['username'],
                'joined_at' => $member['joined_at'],
                'on_platform' => $localUser !== null,
                'video_percent' => $video?->percent,
                'video_complete' => $video?->complete ?? false,
                'document_percent' => $documents?->percent,
                'document_complete' => $documents?->complete ?? false,
            ];
        })->values();

        $ownVideo = $this->videoProgress($user);
        $ownDocuments = $this->documentProgress(
            $publishedContentIds,
            $totalContentCount,
            ContentView::where('user_id', $user->id)->get()
        );

        $totalMembers = $tree['total_team'] ?? $rows->count();
        $purchasedCount = $rows->filter(fn ($row) => filled($row->purchase_code))->count();
        $videosCompleteCount = $rows->filter(fn ($row) => $row->video_complete)->count();
        $documentsCompleteCount = $rows->filter(fn ($row) => $row->document_complete)->count();

        return view('user.team.index', [
            'apiError' => false,
            'user' => $user,
            'truncated' => $tree['truncated'] ?? false,
            'rootNode' => $rootNode,
            'rootPurchase' => $rootPurchase,
            'ownVideoPercent' => $ownVideo->percent,
            'ownDocumentPercent' => $ownDocuments->percent,
            'rows' => $rows,
            'stats' => [
                'total_members' => $totalMembers,
                'purchased_count' => $purchasedCount,
                'purchased_percent' => $totalMembers > 0 ? (int) round($purchasedCount / $totalMembers * 100) : 0,
                'videos_complete_count' => $videosCompleteCount,
                'videos_complete_percent' => $totalMembers > 0 ? (int) round($videosCompleteCount / $totalMembers * 100) : 0,
                'documents_complete_count' => $documentsCompleteCount,
                'documents_complete_percent' => $totalMembers > 0 ? (int) round($documentsCompleteCount / $totalMembers * 100) : 0,
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
     * Aggregate lesson-completion % across every published course assigned
     * to the user, reusing Course::progressFor() (the same logic behind
     * "Learning Progress" elsewhere) rather than a new scoring method.
     */
    private function videoProgress(User $user): object
    {
        $courses = $user->assignedCourses()->where('is_published', true)->get();

        if ($courses->isEmpty()) {
            return (object) ['percent' => null, 'complete' => false];
        }

        $completed = 0;
        $total = 0;

        foreach ($courses as $course) {
            $progress = $course->progressFor($user);
            $completed += $progress->completed_lessons;
            $total += $progress->total_lessons;
        }

        $percent = $total > 0 ? (int) round($completed / $total * 100) : 0;

        return (object) ['percent' => $percent, 'complete' => $total > 0 && $completed === $total];
    }

    /**
     * @return array{0: array<string, Collection>, 1: int}
     */
    private function publishedContentIds(): array
    {
        $ids = [
            Document::class => Document::published()->pluck('id'),
            SalesToolkitItem::class => SalesToolkitItem::published()->pluck('id'),
            ScriptItem::class => ScriptItem::published()->pluck('id'),
            SalesManual::class => SalesManual::published()->pluck('id'),
        ];

        $total = collect($ids)->sum(fn (Collection $idCollection) => $idCollection->count());

        return [$ids, $total];
    }

    /**
     * Only counts views against content that is still published — mirrors
     * the convention in Admin\OnboardingChecklistController::progress().
     */
    private function documentProgress(array $publishedIds, int $totalContentCount, Collection $userViews): object
    {
        if ($totalContentCount === 0) {
            return (object) ['percent' => null, 'complete' => false];
        }

        $viewedCount = $userViews->filter(function ($view) use ($publishedIds) {
            return isset($publishedIds[$view->viewable_type])
                && $publishedIds[$view->viewable_type]->contains($view->viewable_id);
        })->count();

        $percent = (int) round($viewedCount / $totalContentCount * 100);

        return (object) ['percent' => $percent, 'complete' => $viewedCount >= $totalContentCount];
    }
}

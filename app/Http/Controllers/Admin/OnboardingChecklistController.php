<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingChecklistCompletion;
use App\Models\OnboardingChecklistItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingChecklistController extends Controller
{
    public function index(): View
    {
        $items = OnboardingChecklistItem::ordered()->withCount('completions')->get();

        return view('admin.onboarding-checklist.index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        OnboardingChecklistItem::create($this->validateItem($request));

        return redirect()->route('admin.onboarding-checklist.index')->with('status', 'Checklist item added.');
    }

    public function update(Request $request, OnboardingChecklistItem $onboardingChecklistItem): RedirectResponse
    {
        $onboardingChecklistItem->update($this->validateItem($request));

        return redirect()->route('admin.onboarding-checklist.index')->with('status', 'Checklist item updated.');
    }

    public function destroy(OnboardingChecklistItem $onboardingChecklistItem): RedirectResponse
    {
        $onboardingChecklistItem->delete();

        return redirect()->route('admin.onboarding-checklist.index')->with('status', 'Checklist item deleted.');
    }

    public function togglePublish(Request $request, OnboardingChecklistItem $onboardingChecklistItem): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $onboardingChecklistItem->update($data);

        return back()->with('status', $onboardingChecklistItem->is_published ? "\"{$onboardingChecklistItem->title}\" published." : "\"{$onboardingChecklistItem->title}\" set to draft.");
    }

    /**
     * A matrix of every salesperson × every published checklist item, so an
     * admin can see at a glance who has completed what and who is still
     * behind — the "which completed and which left" view.
     */
    public function progress(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $items = OnboardingChecklistItem::published()->ordered()->get();

        $users = User::where('role', 'user')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        $itemIds = $items->pluck('id')->all();

        $completedItemIdsByUser = OnboardingChecklistCompletion::whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->pluck('onboarding_checklist_item_id')->all());

        $rows = $users->map(function (User $user) use ($items, $itemIds, $completedItemIdsByUser) {
            // Only count completions against items that are still published —
            // a completion left over from a since-unpublished/deleted item
            // must not inflate the count beyond what the matrix actually shows.
            $completedIds = array_values(array_intersect($completedItemIdsByUser[$user->id] ?? [], $itemIds));

            return (object) [
                'user' => $user,
                'completed_ids' => $completedIds,
                'completed_count' => count($completedIds),
                'percent' => $items->isEmpty() ? 0 : (int) round(count($completedIds) / $items->count() * 100),
            ];
        });

        return view('admin.onboarding-checklist.progress', [
            'items' => $items,
            'rows' => $rows,
        ]);
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'link' => ['nullable', 'url', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}

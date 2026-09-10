<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingChecklistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingChecklistController extends Controller
{
    public function index(Request $request): View
    {
        $items = OnboardingChecklistItem::published()->ordered()->paginate(10)->withQueryString();

        $completedIds = $request->user()->onboardingChecklistCompletions()->pluck('onboarding_checklist_item_id');

        $totalCount = OnboardingChecklistItem::published()->count();
        $completedCount = OnboardingChecklistItem::published()->whereIn('id', $completedIds)->count();

        return view('user.onboarding-checklist.index', [
            'items' => $items,
            'completedIds' => $completedIds,
            'totalCount' => $totalCount,
            'completedCount' => $completedCount,
        ]);
    }

    public function toggle(Request $request, OnboardingChecklistItem $onboardingChecklistItem): RedirectResponse
    {
        $user = $request->user();

        $completion = $user->onboardingChecklistCompletions()
            ->where('onboarding_checklist_item_id', $onboardingChecklistItem->id)
            ->first();

        if ($completion) {
            // Unticking is never blocked — a user correcting their own
            // over-eager tick isn't something that needs verifying.
            $completion->delete();

            return back();
        }

        // Ticking is only blocked when this item is confidently matched to a
        // real course/resource/assessment AND that isn't actually finished
        // yet. A null result (no match found) means there's nothing to
        // verify, so it's still a plain self-reported tick, same as always.
        if ($onboardingChecklistItem->verifyCompletionFor($user) === false) {
            return back()
                ->with('error', "You haven't completed \"{$onboardingChecklistItem->title}\" yet — finish it first, then check it off here.")
                ->with('completion_target', $onboardingChecklistItem->completionTarget());
        }

        $user->onboardingChecklistCompletions()->create([
            'onboarding_checklist_item_id' => $onboardingChecklistItem->id,
            'completed_at' => now(),
        ]);

        return back();
    }
}

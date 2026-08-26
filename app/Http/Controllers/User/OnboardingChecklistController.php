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
        $items = OnboardingChecklistItem::published()->ordered()->get();

        $completedIds = $request->user()->onboardingChecklistCompletions()->pluck('onboarding_checklist_item_id');

        return view('user.onboarding-checklist.index', [
            'items' => $items,
            'completedIds' => $completedIds,
        ]);
    }

    public function toggle(Request $request, OnboardingChecklistItem $onboardingChecklistItem): RedirectResponse
    {
        $completion = $request->user()->onboardingChecklistCompletions()
            ->where('onboarding_checklist_item_id', $onboardingChecklistItem->id)
            ->first();

        if ($completion) {
            $completion->delete();
        } else {
            $request->user()->onboardingChecklistCompletions()->create([
                'onboarding_checklist_item_id' => $onboardingChecklistItem->id,
                'completed_at' => now(),
            ]);
        }

        return back();
    }
}

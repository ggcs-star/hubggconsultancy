<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::withCount('leads')->latest()->get();

        return view('admin.campaigns.index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateCampaign($request);
        $data['created_by'] = auth()->id();

        $campaign = Campaign::create($data);

        if ($request->wantsJson()) {
            return response()->json(['id' => $campaign->id, 'name' => $campaign->name]);
        }

        return redirect()->route('admin.campaigns.index')->with('status', 'Campaign added.');
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($this->validateCampaign($request));

        return redirect()->route('admin.campaigns.index')->with('status', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('admin.campaigns.index')->with('status', 'Campaign deleted.');
    }

    public function toggleActive(Request $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);

        $campaign->update($data);

        return back()->with('status', $campaign->is_active ? "\"{$campaign->name}\" activated." : "\"{$campaign->name}\" set to inactive.");
    }

    public function show(Campaign $campaign): View
    {
        $campaign->load(['leads.assignee']);

        return view('admin.campaigns.show', [
            'campaign' => $campaign,
            'metrics' => $campaign->metrics(),
        ]);
    }

    private function validateCampaign(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
    }
}

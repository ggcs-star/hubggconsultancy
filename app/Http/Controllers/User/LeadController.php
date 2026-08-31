<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $campaignId = trim((string) $request->query('campaign_id'));
        $product = trim((string) $request->query('product'));
        $valueMin = trim((string) $request->query('value_min'));
        $valueMax = trim((string) $request->query('value_max'));
        $followUpFrom = trim((string) $request->query('follow_up_from'));
        $followUpTo = trim((string) $request->query('follow_up_to'));
        $userId = $request->user()->id;

        $myLeads = Lead::assignedTo($userId)->get();

        $leads = Lead::assignedTo($userId)
            ->with('campaign')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($campaignId !== '', fn ($query) => $query->where('campaign_id', $campaignId))
            ->when($product !== '', fn ($query) => $query->where('product', 'like', "%{$product}%"))
            ->when($valueMin !== '', fn ($query) => $query->where('expected_value', '>=', $valueMin))
            ->when($valueMax !== '', fn ($query) => $query->where('expected_value', '<=', $valueMax))
            ->when($followUpFrom !== '', fn ($query) => $query->whereDate('next_follow_up_at', '>=', $followUpFrom))
            ->when($followUpTo !== '', fn ($query) => $query->whereDate('next_follow_up_at', '<=', $followUpTo))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.leads.index', [
            'leads' => $leads,
            'statusLabels' => Lead::statusLabels(),
            'campaigns' => Campaign::orderByDesc('created_at')->get(),
            'stats' => [
                'new' => $this->statWithTrend($myLeads, fn (Lead $lead) => $lead->status === 'new'),
                'follow_ups' => $this->statWithTrend($myLeads, fn (Lead $lead) => $lead->isOverdue() || ($lead->next_follow_up_at && $lead->next_follow_up_at->isToday())),
                'qualified' => $this->statWithTrend($myLeads, fn (Lead $lead) => $lead->status === 'qualified'),
                'opportunities' => $this->statWithTrend($myLeads, fn (Lead $lead) => in_array($lead->status, ['proposal', 'negotiation'], true)),
                'won' => $this->statWithTrend($myLeads, fn (Lead $lead) => $lead->status === 'won', 'won_at'),
            ],
        ]);
    }

    private function statWithTrend($leads, callable $matches, string $dateField = 'created_at'): array
    {
        $value = $leads->filter($matches)->count();

        $now = now();
        $currentStart = $now->copy()->subDays(7);
        $previousStart = $now->copy()->subDays(14);

        $inWindow = fn (Lead $lead, $start, $end) => $lead->{$dateField} && $lead->{$dateField}->between($start, $end);

        $current = $leads->filter(fn (Lead $lead) => $matches($lead) && $inWindow($lead, $currentStart, $now))->count();
        $previous = $leads->filter(fn (Lead $lead) => $matches($lead) && $inWindow($lead, $previousStart, $currentStart))->count();

        $trend = $previous > 0
            ? (int) round((($current - $previous) / $previous) * 100)
            : ($current > 0 ? 100 : 0);

        return ['value' => $value, 'trend' => $trend];
    }

    public function create(): View
    {
        return view('user.leads.create', [
            'campaigns' => Campaign::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'product' => ['nullable', 'string', 'max:255'],
            'expected_value' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['required', 'in:low,medium,high'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $data['status'] = 'new';
        $data['assigned_to'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $duplicates = ! empty($data['phone']) ? Lead::duplicatesFor($data['phone']) : collect();

        $lead = Lead::create($data);

        $status = 'Lead added.';
        if ($duplicates->isNotEmpty()) {
            $status .= ' Note: ' . $duplicates->count() . ' existing lead(s) already share this phone number — check for duplicates.';
        }

        return redirect()->route('user.leads.show', $lead)->with('status', $status);
    }

    public function edit(Request $request, Lead $lead): View
    {
        abort_unless($lead->assigned_to === $request->user()->id, 403);

        return view('user.leads.edit', [
            'lead' => $lead,
            'campaigns' => Campaign::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($lead->assigned_to === $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'product' => ['nullable', 'string', 'max:255'],
            'expected_value' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:' . implode(',', array_keys(Lead::statusLabels()))],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $lead->update($data);

        return redirect()->route('user.leads.index')->with('status', 'Lead updated.');
    }

    public function destroy(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($lead->assigned_to === $request->user()->id, 403);

        $lead->delete();

        return redirect()->route('user.leads.index')->with('status', 'Lead deleted.');
    }

    public function storeCampaign(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $data['created_by'] = $request->user()->id;

        $campaign = Campaign::create($data);

        if ($request->wantsJson()) {
            return response()->json(['id' => $campaign->id, 'name' => $campaign->name]);
        }

        return back()->with('status', 'Campaign added.');
    }

    public function show(Request $request, Lead $lead): View
    {
        abort_unless($lead->assigned_to === $request->user()->id, 403);

        $lead->load(['notes.user', 'campaign', 'assignee', 'creator']);

        return view('user.leads.show', [
            'lead' => $lead,
            'statusLabels' => Lead::statusLabels(),
        ]);
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($lead->assigned_to === $request->user()->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Lead::statusLabels()))],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $lead->update($data);

        return back()->with('status', 'Lead updated.');
    }

    public function storeNote(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($lead->assigned_to === $request->user()->id, 403);

        $data = $request->validate(['note' => ['required', 'string', 'max:2000']]);

        $lead->notes()->create([
            'user_id' => $request->user()->id,
            'note' => $data['note'],
        ]);

        return redirect()->route('user.leads.show', $lead)->with('status', 'Note added.');
    }
}

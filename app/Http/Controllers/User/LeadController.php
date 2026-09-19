<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use App\Traits\ResolvesPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    use ResolvesPeriod;

    public function index(Request $request): View
    {
        $period = in_array($request->query('period'), ['today', 'week', 'month'], true) ? $request->query('period') : 'all';
        [$periodFrom, $periodTo] = $this->resolvePeriodRange($period);

        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $campaignId = trim((string) $request->query('campaign_id'));
        $product = trim((string) $request->query('product'));
        $followUpFrom = trim((string) $request->query('follow_up_from'));
        $followUpTo = trim((string) $request->query('follow_up_to'));
        $userId = $request->user()->id;

        $myLeads = Lead::assignedTo($userId)
            ->when($periodFrom, fn ($query) => $query->where(function ($query) use ($periodFrom, $periodTo) {
                $query->whereBetween('created_at', [$periodFrom, $periodTo])
                    ->orWhereBetween('next_follow_up_at', [$periodFrom, $periodTo]);
            }))
            ->get();

        $leads = Lead::assignedTo($userId)
            ->with(['campaign', 'notes.user'])
            ->when($periodFrom, fn ($query) => $query->where(function ($query) use ($periodFrom, $periodTo) {
                $query->whereBetween('created_at', [$periodFrom, $periodTo])
                    ->orWhereBetween('next_follow_up_at', [$periodFrom, $periodTo]);
            }))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($campaignId !== '', fn ($query) => $query->where('campaign_id', $campaignId))
            ->when($product !== '', fn ($query) => $query->where('product', 'like', "%{$product}%"))
            ->when($followUpFrom !== '', fn ($query) => $query->whereDate('next_follow_up_at', '>=', $followUpFrom))
            ->when($followUpTo !== '', fn ($query) => $query->whereDate('next_follow_up_at', '<=', $followUpTo))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.leads.index', [
            'leads' => $leads,
            'period' => $period,
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

        return back()->with('status', 'Note added.');
    }
}

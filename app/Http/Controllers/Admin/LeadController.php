<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $assignedTo = trim((string) $request->query('assigned_to'));
        $campaignId = trim((string) $request->query('campaign_id'));
        $product = trim((string) $request->query('product'));
        $valueMin = trim((string) $request->query('value_min'));
        $valueMax = trim((string) $request->query('value_max'));
        $followUpFrom = trim((string) $request->query('follow_up_from'));
        $followUpTo = trim((string) $request->query('follow_up_to'));

        $leads = Lead::query()
            ->with(['assignee', 'campaign'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($assignedTo !== '', fn ($query) => $query->where('assigned_to', $assignedTo))
            ->when($campaignId !== '', fn ($query) => $query->where('campaign_id', $campaignId))
            ->when($product !== '', fn ($query) => $query->where('product', 'like', "%{$product}%"))
            ->when($valueMin !== '', fn ($query) => $query->where('expected_value', '>=', $valueMin))
            ->when($valueMax !== '', fn ($query) => $query->where('expected_value', '<=', $valueMax))
            ->when($followUpFrom !== '', fn ($query) => $query->whereDate('next_follow_up_at', '>=', $followUpFrom))
            ->when($followUpTo !== '', fn ($query) => $query->whereDate('next_follow_up_at', '<=', $followUpTo))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $allLeads = Lead::all();

        return view('admin.leads.index', [
            'leads' => $leads,
            'salespersons' => $this->salespersons(),
            'campaigns' => Campaign::orderByDesc('created_at')->get(),
            'statusLabels' => Lead::statusLabels(),
            'stats' => $this->dashboardStats($allLeads),
            'funnel' => $this->pipelineFunnel($allLeads),
        ]);
    }

    public function create(): View
    {
        return view('admin.leads.create', [
            'lead' => null,
            'salespersons' => $this->salespersons(),
            'campaigns' => Campaign::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateLead($request);
        $data['created_by'] = auth()->id();

        $duplicates = ! empty($data['phone']) ? Lead::duplicatesFor($data['phone']) : collect();

        $lead = Lead::create($data);

        $status = 'Lead added.';
        if ($duplicates->isNotEmpty()) {
            $status .= ' Note: ' . $duplicates->count() . ' existing lead(s) already share this phone number — check for duplicates.';
        }

        return redirect()->route('admin.leads.show', $lead)->with('status', $status);
    }

    public function edit(Lead $lead): View
    {
        return view('admin.leads.edit', [
            'lead' => $lead,
            'salespersons' => $this->salespersons(),
            'campaigns' => Campaign::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $lead->update($this->validateLead($request));

        return redirect()->route('admin.leads.index')->with('status', 'Lead updated.');
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Lead::statusLabels()))],
        ]);

        $lead->update($data);

        return back()->with('status', 'Lead status updated.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['assignee', 'creator', 'campaign', 'notes.user']);

        $duplicates = $lead->phone ? Lead::duplicatesFor($lead->phone, $lead->id) : collect();

        return view('admin.leads.show', [
            'lead' => $lead,
            'statusLabels' => Lead::statusLabels(),
            'duplicates' => $duplicates,
        ]);
    }

    public function storeNote(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:2000']]);

        $lead->notes()->create([
            'user_id' => auth()->id(),
            'note' => $data['note'],
        ]);

        return redirect()->route('admin.leads.show', $lead)->with('status', 'Note added.');
    }

    public function bulkAssign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lead_ids' => ['required', 'array', 'min:1'],
            'lead_ids.*' => ['exists:leads,id'],
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $count = Lead::whereIn('id', $data['lead_ids'])->update(['assigned_to' => $data['assigned_to']]);

        return redirect()->route('admin.leads.index')->with('status', "{$count} lead(s) assigned.");
    }

    public function autoAssign(): RedirectResponse
    {
        $unassigned = Lead::whereNull('assigned_to')->orderBy('id')->get();
        $salespersons = $this->salespersons();

        if ($salespersons->isEmpty()) {
            return back()->with('status', 'No approved salespersons available to assign leads to.');
        }

        $index = 0;
        foreach ($unassigned as $lead) {
            $lead->update(['assigned_to' => $salespersons[$index % $salespersons->count()]->id]);
            $index++;
        }

        return redirect()->route('admin.leads.index')->with('status', "{$unassigned->count()} unassigned lead(s) distributed round-robin across {$salespersons->count()} salespersons.");
    }

    private function salespersons()
    {
        return User::where('role', 'user')->where('salesperson_status', 'approved')->orderBy('name')->get();
    }

    private function dashboardStats($leads): array
    {
        return [
            'new' => $this->statWithTrend($leads, fn (Lead $lead) => $lead->status === 'new'),
            'follow_ups' => $this->statWithTrend($leads, fn (Lead $lead) => $lead->next_follow_up_at
                && ! in_array($lead->status, Lead::TERMINAL_STATUSES, true)
                && $lead->next_follow_up_at->lte(now())),
            'qualified' => $this->statWithTrend($leads, fn (Lead $lead) => $lead->status === 'qualified'),
            'opportunities' => $this->statWithTrend($leads, fn (Lead $lead) => in_array($lead->status, ['proposal', 'negotiation'], true)),
            'won' => $this->statWithTrend($leads, fn (Lead $lead) => $lead->status === 'won', 'won_at'),
        ];
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

    private function pipelineFunnel($leads): array
    {
        $funnel = [];

        foreach (Lead::PIPELINE_STATUSES as $status) {
            $funnel[$status] = $leads->where('status', $status)->count();
        }

        return $funnel;
    }

    private function validateLead(Request $request): array
    {
        return $request->validate([
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
            'assigned_to' => ['nullable', 'exists:users,id'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);
    }
}

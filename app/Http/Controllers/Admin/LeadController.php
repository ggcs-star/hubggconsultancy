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
            ->latest()
            ->paginate(20)
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
            'new' => $leads->where('status', 'new')->count(),
            'follow_ups' => $leads->filter(fn (Lead $lead) => $lead->next_follow_up_at
                && ! in_array($lead->status, Lead::TERMINAL_STATUSES, true)
                && $lead->next_follow_up_at->lte(now()))->count(),
            'qualified' => $leads->where('status', 'qualified')->count(),
            'opportunities' => $leads->whereIn('status', ['proposal', 'negotiation'])->count(),
            'won' => $leads->where('status', 'won')->count(),
        ];
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

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
        $userId = $request->user()->id;

        $myLeads = Lead::assignedTo($userId)->get();

        $leads = Lead::assignedTo($userId)
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('user.leads.index', [
            'leads' => $leads,
            'statusLabels' => Lead::statusLabels(),
            'stats' => [
                'new' => $myLeads->where('status', 'new')->count(),
                'follow_ups' => $myLeads->filter(fn (Lead $lead) => $lead->isOverdue() || ($lead->next_follow_up_at && $lead->next_follow_up_at->isToday()))->count(),
                'qualified' => $myLeads->where('status', 'qualified')->count(),
                'opportunities' => $myLeads->whereIn('status', ['proposal', 'negotiation'])->count(),
                'won' => $myLeads->where('status', 'won')->count(),
            ],
        ]);
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

        $lead->load(['notes.user', 'campaign']);

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

        return redirect()->route('user.leads.show', $lead)->with('status', 'Lead updated.');
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

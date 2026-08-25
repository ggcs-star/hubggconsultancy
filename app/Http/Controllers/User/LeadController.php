<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $myLeads = Lead::assignedTo($request->user()->id)->get();

        $leads = collect($myLeads)
            ->when($search !== '', fn ($collection) => $collection->filter(fn (Lead $lead) => str_contains(strtolower($lead->name), strtolower($search))
                || str_contains(strtolower((string) $lead->email), strtolower($search))
                || str_contains((string) $lead->phone, $search)
                || str_contains(strtolower((string) $lead->company), strtolower($search))))
            ->when($status !== '', fn ($collection) => $collection->where('status', $status))
            ->sortByDesc('created_at')
            ->values();

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

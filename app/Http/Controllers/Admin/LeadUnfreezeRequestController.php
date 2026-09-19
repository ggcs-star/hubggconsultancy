<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadUnfreezeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status', 'pending');

        $requests = Lead::with(['assignee', 'unfreezeReviewer'])
            ->where('unfreeze_status', '!=', 'none')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('assignee', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('unfreeze_status', $status))
            ->orderByDesc('unfreeze_requested_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.lead-unfreeze-requests', [
            'requests' => $requests,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function approve(Lead $lead): RedirectResponse
    {
        $lead->update([
            'unfreeze_status' => 'approved',
            'unfreeze_reviewed_at' => now(),
            'unfreeze_reviewed_by' => auth()->id(),
            // Restarts the freeze clock — the same effect as if the salesperson had just touched it.
            'status_changed_at' => now(),
        ]);

        return back()->with('status', "The unfreeze request for \"{$lead->name}\" was approved.");
    }

    public function reject(Lead $lead): RedirectResponse
    {
        $lead->update([
            'unfreeze_status' => 'rejected',
            'unfreeze_reviewed_at' => now(),
            'unfreeze_reviewed_by' => auth()->id(),
        ]);

        return back()->with('status', "The unfreeze request for \"{$lead->name}\" was rejected.");
    }
}

<?php

namespace App\Services\Leads;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\NewLeadsImportedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Fires the two email notifications tied to lead imports — an admin summary
 * and per-salesperson "you've got new leads" emails — shared by both the
 * manual CSV import and the Drive auto-sync, since both funnel through
 * LeadCsvService::importRows().
 */
class LeadImportNotifier
{
    /** @param  Lead[]  $createdLeads */
    public function notify(array $createdLeads): void
    {
        if (empty($createdLeads)) {
            return;
        }

        $leads = collect($createdLeads);

        $this->notifyAdmins($leads);
        $this->notifyAssignees($leads);
    }

    private function notifyAdmins(Collection $leads): void
    {
        $emails = config('leads.notify_emails');

        if (empty($emails)) {
            return;
        }

        $unassigned = $leads->whereNull('assigned_to')->values();

        Notification::route('mail', $emails)
            ->notify(new NewLeadsImportedNotification($leads->count(), $unassigned));
    }

    private function notifyAssignees(Collection $leads): void
    {
        $byAssignee = $leads->whereNotNull('assigned_to')->groupBy('assigned_to');

        if ($byAssignee->isEmpty()) {
            return;
        }

        $users = User::whereIn('id', $byAssignee->keys())->get()->keyBy('id');

        foreach ($byAssignee as $userId => $userLeads) {
            $users->get($userId)?->notify(new LeadAssignedNotification($userLeads));
        }
    }
}

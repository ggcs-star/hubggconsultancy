<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * Sent to a salesperson when a CSV import or Drive sync assigns one or more
 * new leads to them — grouped into one email per person per batch, not one
 * email per lead.
 */
class LeadAssignedNotification extends Notification
{
    use Queueable;

    /** @param  Collection<int, \App\Models\Lead>  $leads */
    public function __construct(private readonly Collection $leads)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->leads->count();
        $days = config('leads.freeze_after_days');

        return (new MailMessage())
            ->subject($count === 1 ? "New Lead Assigned to You" : "{$count} New Leads Assigned to You")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been assigned {$count} new lead" . ($count === 1 ? '' : 's') . ' in Global Garner Hub. Please review the details below and follow up with each lead at the earliest.')
            ->line($this->leadsTable())
            ->line('Please contact the assigned leads and update their status in the CRM after your follow-up.')
            ->line("**Important:** If a lead's status is not updated within {$days} day(s), the lead will be automatically frozen. An admin approval will be required to unfreeze the lead.")
            ->line('Please ensure that the lead status is updated within the required timeframe.')
            ->action('View My Leads', route('user.leads.index'));
    }

    /** See NewLeadsImportedNotification::unassignedLeadsTable() for why this is hand-built HTML wrapped in HtmlString rather than a markdown table string. */
    private function leadsTable(): HtmlString
    {
        $rows = $this->leads->map(function ($lead) {
            $cells = [
                $lead->name,
                $lead->company ?: '—',
                $lead->product ?: '—',
                $lead->campaign?->name ?: '—',
                $lead->source ?: '—',
            ];
            $tds = collect($cells)->map(fn ($cell) => '<td style="padding:8px 12px;border:1px solid #e8e5ef;">' . e($cell) . '</td>')->implode('');

            return "<tr>{$tds}</tr>";
        })->implode('');

        $headerCells = collect(['Lead Name', 'Company', 'Product', 'Campaign', 'Source'])
            ->map(fn ($label) => '<th style="padding:8px 12px;border:1px solid #e8e5ef;background:#edf2f7;text-align:left;">' . $label . '</th>')
            ->implode('');

        return new HtmlString(
            '<table style="border-collapse:collapse;width:100%;margin:0 0 16px;font-size:14px;">'
            . "<thead><tr>{$headerCells}</tr></thead>"
            . "<tbody>{$rows}</tbody>"
            . '</table>'
        );
    }
}

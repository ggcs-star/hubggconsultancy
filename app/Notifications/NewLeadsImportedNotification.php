<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * Sent to the admin notify list (config('leads.notify_emails')) after a CSV
 * import or Drive sync adds at least one new lead — a batch summary, not one
 * email per lead, so a large import doesn't flood the inbox.
 */
class NewLeadsImportedNotification extends Notification
{
    use Queueable;

    /** @param  Collection<int, \App\Models\Lead>  $unassignedLeads */
    public function __construct(
        private readonly int $totalImported,
        private readonly Collection $unassignedLeads,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = now()->format('d M Y');
        $count = $this->totalImported;

        $mail = (new MailMessage())
            ->subject("{$count} New Lead" . ($count === 1 ? '' : 's') . " Imported – {$date}")
            ->greeting('Hello Admin,')
            ->line("We're pleased to inform you that {$count} new lead" . ($count === 1 ? ' was' : 's were') . " successfully imported into your Leads/CRM on {$date}.");

        if ($this->unassignedLeads->isNotEmpty()) {
            $unassignedCount = $this->unassignedLeads->count();

            $mail->line('**Lead Assignment Status**')
                ->line("{$unassignedCount} lead" . ($unassignedCount === 1 ? ' is' : 's are') . ' currently unassigned and requires your attention.')
                ->line($this->unassignedLeadsTable());
        }

        return $mail
            ->line('Please review the newly imported leads and assign the unassigned lead' . ($this->unassignedLeads->count() === 1 ? '' : 's') . ' to the appropriate member of your sales team to ensure timely follow-up.')
            ->action('View Leads', route('admin.leads.index'));
    }

    /**
     * MailMessage::line() collapses embedded newlines into spaces for plain
     * strings (see SimpleMessage::formatLine()) — fatal for a markdown table,
     * which needs each row on its own line. Passing an Htmlable instance
     * instead skips that collapsing entirely, so the table is hand-built as
     * plain HTML here (Laravel's own <x-mail::table> component can't be used
     * — its "mail::" view namespace only exists during Markdown::render()'s
     * own pass, which happens after toMail() has already returned) and
     * wrapped in HtmlString to reach the email untouched.
     */
    private function unassignedLeadsTable(): HtmlString
    {
        $rows = $this->unassignedLeads->map(function ($lead) {
            $nameCell = e($lead->name);

            if ($lead->campaign) {
                $nameCell .= '<br><span style="font-size:12px;color:#718096;">' . e($lead->campaign->name) . '</span>';
            }

            $cells = [
                $nameCell,
                e($lead->phone ?: '—'),
                e($lead->product ?: '—'),
            ];
            $tds = collect($cells)->map(fn ($cell) => '<td style="padding:8px 12px;border:1px solid #e8e5ef;">' . $cell . '</td>')->implode('');

            return "<tr>{$tds}</tr>";
        })->implode('');

        $headerCells = collect(['Name', 'Contact', 'Product'])
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

<?php

namespace App\Services\Leads;

use App\Models\Campaign;
use App\Models\Lead;
use App\Support\Csv\ExcelSafeCsv;
use App\Traits\HasApprovedSalespersons;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV export/import for leads. Column order mirrors the Add Lead form (see
 * resources/views/admin/leads/_form-fields.blade.php) so an exported file can be
 * edited and re-imported as-is, and so admins recognize the fields immediately.
 */
class LeadCsvService
{
    use HasApprovedSalespersons;

    /** @var string[] */
    private const COLUMNS = [
        'Name', 'Company', 'Email', 'Phone', 'Product', 'Source', 'Campaign',
        'Priority', 'Assigned To', 'Status', 'Next Follow-up (YYYY-MM-DD)',
    ];

    public function streamExport(array $filters): StreamedResponse
    {
        $leads = Lead::query()
            ->with(['assignee', 'campaign'])
            ->filter($filters)
            ->latest()
            ->get();

        $columns = [...self::COLUMNS, 'Created On'];

        return response()->streamDownload(function () use ($leads, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->name,
                    $lead->company,
                    $lead->email,
                    ExcelSafeCsv::guard($lead->phone),
                    $lead->product,
                    $lead->source,
                    $lead->campaign?->name,
                    $lead->priority,
                    $lead->assignee?->name,
                    $lead->statusLabel(),
                    $lead->next_follow_up_at?->format('Y-m-d'),
                    $lead->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 'leads-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function streamSample(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::COLUMNS);
            fputcsv($handle, [
                'Jane Doe', 'Acme Pvt Ltd', 'jane@example.com', '9876543210', 'UPOS', 'Website',
                'GG Prime August Campaign', 'medium', $this->salespersons()->first()?->name ?? 'Rohit Malhotra', 'New',
                now()->addDays(3)->format('Y-m-d'),
            ]);
            fclose($handle);
        }, 'leads-import-sample.csv', ['Content-Type' => 'text/csv']);
    }

    public function importFromFile(UploadedFile $file, ?int $importedBy): LeadImportResult
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            return new LeadImportResult();
        }

        $header = array_map(fn ($column) => strtolower(trim((string) $column)), $header);
        $columnCount = count($header);

        // Same names as the "Assign To" dropdown on the Add/Edit Lead form — matched by name, not id/email.
        $salespersonsByName = $this->salespersons()->mapWithKeys(
            fn ($user) => [$this->normalizeName($user->name) => $user->id]
        );

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = array_slice(array_pad($row, $columnCount, null), 0, $columnCount);
            $data = array_combine($header, $row);

            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                $errors++;

                continue;
            }

            $phone = ExcelSafeCsv::unwrap(trim((string) ($data['phone'] ?? '')));

            if ($phone !== '' && Lead::duplicatesFor($phone)->contains(fn ($lead) => $lead->name === $name)) {
                $skipped++;

                continue;
            }

            $priority = strtolower(trim((string) ($data['priority'] ?? '')));
            if (! in_array($priority, ['low', 'medium', 'high'], true)) {
                $priority = 'medium';
            }

            $status = Lead::resolveStatusFromInput($data['status'] ?? null);

            $campaignId = null;
            $campaignName = trim((string) ($data['campaign'] ?? ''));
            if ($campaignName !== '') {
                $campaignId = Campaign::firstOrCreate(
                    ['name' => $campaignName],
                    ['created_by' => $importedBy]
                )->id;
            }

            // Accept older template headers too ("Assigned To Email", "Assigned_To"), not just the current "Assigned To".
            $assigneeName = $this->firstPresent($data, ['assigned to', 'assigned to email', 'assigned_to', 'assignedto']);
            $assignedTo = $assigneeName !== '' ? $salespersonsByName->get($this->normalizeName($assigneeName)) : null;

            $nextFollowUp = trim((string) ($data['next follow-up (yyyy-mm-dd)'] ?? ''));
            if ($nextFollowUp !== '') {
                try {
                    $nextFollowUp = Carbon::parse($nextFollowUp)->toDateString();
                } catch (\Exception) {
                    $nextFollowUp = null;
                }
            } else {
                $nextFollowUp = null;
            }

            try {
                Lead::create([
                    'name' => $name,
                    'email' => trim((string) ($data['email'] ?? '')) ?: null,
                    'phone' => $phone ?: null,
                    'company' => trim((string) ($data['company'] ?? '')) ?: null,
                    'source' => trim((string) ($data['source'] ?? '')) ?: null,
                    'campaign_id' => $campaignId,
                    'product' => trim((string) ($data['product'] ?? '')) ?: null,
                    'priority' => $priority,
                    'status' => $status,
                    'assigned_to' => $assignedTo,
                    'next_follow_up_at' => $nextFollowUp,
                    'created_by' => $importedBy,
                ]);
                $imported++;
            } catch (\Exception) {
                $errors++;
            }
        }

        fclose($handle);

        return new LeadImportResult($imported, $skipped, $errors);
    }

    private function normalizeName(string $value): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($value)));
    }

    /** Returns the first non-empty value found in $data for any of the given (already-lowercased) header keys. */
    private function firstPresent(array $data, array $keys): string
    {
        foreach ($keys as $key) {
            $value = trim((string) ($data[$key] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}

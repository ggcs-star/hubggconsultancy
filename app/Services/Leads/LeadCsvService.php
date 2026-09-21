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
 *
 * "Assigned To" holds the salesperson's phone number, not their name — two
 * approved salespersons can share a name, but phone numbers are unique, so
 * matching on phone is the only way to assign to the right one reliably.
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
                    ExcelSafeCsv::guard($lead->assignee?->phone),
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
                'GG Prime August Campaign', 'medium', ExcelSafeCsv::guard($this->salespersons()->first()?->phone ?? '9123456780'), 'New',
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

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $this->importRows($header, $rows, $importedBy);
    }

    /**
     * Same row-processing rules as importFromFile(), for callers that already
     * have header + row arrays in hand instead of a CSV file on disk — e.g.
     * DriveLeadSyncService, which reads an .xlsx via PhpSpreadsheet.
     *
     * @param  string[]  $header  Raw header row, any case/whitespace (normalized internally).
     * @param  iterable<array<int, mixed>>  $rows  Each row aligned by position to $header.
     */
    public function importRows(array $header, iterable $rows, ?int $importedBy): LeadImportResult
    {
        $header = array_map(fn ($column) => strtolower(trim((string) $column)), $header);
        $columnCount = count($header);

        // Matched by phone, not name — two salespersons can share a name (e.g. "Anuj Singh"),
        // but phone numbers are unique, so "Assigned To" must contain the salesperson's phone number.
        $salespersonsByPhone = $this->salespersons()
            ->filter(fn ($user) => filled($user->phone))
            ->mapWithKeys(fn ($user) => [$this->normalizePhone($user->phone) => $user->id]);

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rows as $row) {
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
            $assigneePhone = $this->firstPresent($data, ['assigned to', 'assigned to email', 'assigned_to', 'assignedto']);
            $assignedTo = $assigneePhone !== ''
                ? $salespersonsByPhone->get($this->normalizePhone(ExcelSafeCsv::unwrap($assigneePhone)))
                : null;

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

        return new LeadImportResult($imported, $skipped, $errors);
    }

    /** Strips formatting and any country code by keeping only the last 10 digits, so "+91 98765 43210" matches a plain "9876543210". */
    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return substr($digits, -10);
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

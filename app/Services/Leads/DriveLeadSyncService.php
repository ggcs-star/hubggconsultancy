<?php

namespace App\Services\Leads;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

/**
 * Pulls the leads sheet from Google Drive (service-account access — the file
 * is private, shared only with that account) and runs it through the exact
 * same row-processing rules as the manual CSV import
 * (LeadCsvService::importRows()), so a lead landing in the sheet via the
 * Meta/Zapier flow behaves identically to one an admin imports by hand.
 *
 * The configured file is a native Google Sheet, not an uploaded .xlsx — Drive
 * only serves raw bytes for actual uploaded files via files.get(alt=media);
 * a native Sheet has to be converted on the fly via files.export() instead,
 * which is what makes this different from a plain file download.
 *
 * Re-running this safely re-imports the whole sheet every time — rows already
 * imported are skipped by the existing name+phone duplicate check, so there's
 * no need to track "which rows were already seen" separately.
 */
class DriveLeadSyncService
{
    public function __construct(private readonly LeadCsvService $leadCsvService)
    {
    }

    public function sync(): LeadImportResult
    {
        $credentialsPath = config('services.google_drive_leads.credentials_path');
        $fileId = config('services.google_drive_leads.file_id');

        if (! $credentialsPath || ! $fileId) {
            throw new RuntimeException('Google Drive lead sync is not configured — set GOOGLE_DRIVE_CREDENTIALS_PATH and GOOGLE_DRIVE_LEADS_FILE_ID in .env.');
        }

        $absoluteCredentialsPath = str_starts_with($credentialsPath, '/') || preg_match('/^[A-Za-z]:\\\\/', $credentialsPath)
            ? $credentialsPath
            : base_path($credentialsPath);

        if (! is_file($absoluteCredentialsPath)) {
            throw new RuntimeException("Google service account key not found at {$absoluteCredentialsPath}.");
        }

        $client = new GoogleClient();
        $client->setAuthConfig($absoluteCredentialsPath);
        $client->addScope(GoogleDrive::DRIVE_READONLY);

        $drive = new GoogleDrive($client);

        $tempPath = tempnam(sys_get_temp_dir(), 'drive-leads-') . '.xlsx';

        try {
            // Binary downloads (export/alt=media) need the client deferred —
            // otherwise the client's own response handling reads the stream
            // first (checking for a JSON error body), leaving its pointer at
            // EOF by the time we get to read it here. Casting to string
            // forces Guzzle to rewind the stream before reading, where
            // ->getContents() alone would just return "".
            $client->setDefer(true);
            $request = $drive->files->export($fileId, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response = $client->execute($request);
            $client->setDefer(false);

            file_put_contents($tempPath, (string) $response->getBody());

            $spreadsheet = IOFactory::load($tempPath);
            $grid = $this->findLeadsGrid($spreadsheet);

            if ($grid === null) {
                Log::warning('Lead sync from Google Drive: no tab with a "Name" + "Phone" header row was found.');

                return new LeadImportResult();
            }

            $header = array_shift($grid);

            $result = $this->leadCsvService->importRows($header, $grid, null);

            Log::info('Lead sync from Google Drive completed.', [
                'imported' => $result->imported,
                'skipped' => $result->skipped,
                'errors' => $result->errors,
            ]);

            return $result;
        } finally {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    /**
     * A Google Sheet can have several tabs — the one with the actual lead
     * data isn't necessarily the "active" one PhpSpreadsheet defaults to when
     * the file is exported, so every tab is checked for a header row
     * containing both "Name" and "Phone" (case-insensitive) before falling
     * back to giving up. Returns the matching tab's full grid (header + rows),
     * or null if no tab looks like a leads sheet at all.
     */
    private function findLeadsGrid(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): ?array
    {
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $grid = $sheet->toArray(null, true, true, false);

            if (empty($grid)) {
                continue;
            }

            $header = array_map(fn ($column) => strtolower(trim((string) $column)), $grid[0]);

            if (in_array('name', $header, true) && in_array('phone', $header, true)) {
                return $grid;
            }
        }

        return null;
    }
}

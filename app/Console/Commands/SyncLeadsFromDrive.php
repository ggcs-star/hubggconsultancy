<?php

namespace App\Console\Commands;

use App\Services\Leads\DriveLeadSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SyncLeadsFromDrive extends Command
{
    protected $signature = 'leads:sync-from-drive';

    protected $description = 'Import new leads from the Google Drive Excel sheet (Meta lead-gen feed) into Leads/CRM.';

    public function handle(DriveLeadSyncService $driveLeadSyncService): int
    {
        try {
            $result = $driveLeadSyncService->sync();
        } catch (Throwable $e) {
            $this->error("Lead sync from Drive failed: {$e->getMessage()}");

            Cache::put('leads_drive_sync_status', [
                'ok' => false,
                'message' => $e->getMessage(),
                'at' => now(),
            ], now()->addDay());

            return self::FAILURE;
        }

        $this->info($result->summary());

        Cache::put('leads_drive_sync_status', [
            'ok' => true,
            'message' => $result->summary(),
            'at' => now(),
        ], now()->addDay());

        return self::SUCCESS;
    }
}

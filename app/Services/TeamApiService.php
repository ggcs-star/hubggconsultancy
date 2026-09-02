<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeamApiService
{
    public function tree(string $userId, int $maxDepth = 6): ?array
    {
        return $this->get('/member/tree', ['user_id' => $userId, 'max_depth' => $maxDepth]);
    }

    public function purchases(string $userId): ?array
    {
        return $this->get('/member/purchases', ['user_id' => $userId]);
    }

    private function get(string $path, array $query): ?array
    {
        $baseUrl = config('services.team_api.base_url');

        if (! $baseUrl) {
            Log::warning('Team API base URL is not configured.');

            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Id' => config('services.team_api.id'),
                'X-Api-Secret' => config('services.team_api.secret'),
            ])->timeout(10)->get(rtrim($baseUrl, '/') . $path, $query);

            if (! $response->successful()) {
                Log::warning('Team API request failed.', ['path' => $path, 'status' => $response->status()]);

                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Team API request error.', ['path' => $path, 'message' => $e->getMessage()]);

            return null;
        }
    }
}

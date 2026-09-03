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

    /**
     * Looks up a GG Prime member by exactly one of user_id, mobile, or
     * email. Unlike tree()/purchases(), callers need to tell "this person
     * isn't a GG Prime member" apart from "the API is down" — those get two
     * different messages at registration/login — so this returns a status
     * instead of collapsing both to null. On success, `data` is the inner
     * profile object (user_id, username, name, mobile, email, ...), already
     * unwrapped from the API's {"status":"success","data":{...}} envelope.
     */
    public function profile(array $query): object
    {
        $baseUrl = config('services.team_api.base_url');

        if (! $baseUrl) {
            Log::warning('Team API base URL is not configured.');

            return (object) ['status' => 'unreachable', 'data' => null];
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Id' => config('services.team_api.id'),
                'X-Api-Secret' => config('services.team_api.secret'),
            ])->timeout(10)->get(rtrim($baseUrl, '/') . '/member/profile', $query);

            if ($response->status() === 404) {
                return (object) ['status' => 'not_found', 'data' => null];
            }

            if (! $response->successful()) {
                Log::warning('Team API request failed.', ['path' => '/member/profile', 'status' => $response->status()]);

                return (object) ['status' => 'unreachable', 'data' => null];
            }

            $body = $response->json();

            if (($body['status'] ?? null) !== 'success' || empty($body['data'])) {
                return (object) ['status' => 'not_found', 'data' => null];
            }

            return (object) ['status' => 'found', 'data' => $body['data']];
        } catch (\Throwable $e) {
            Log::error('Team API request error.', ['path' => '/member/profile', 'message' => $e->getMessage()]);

            return (object) ['status' => 'unreachable', 'data' => null];
        }
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

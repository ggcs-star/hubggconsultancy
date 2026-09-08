<?php

namespace App\Traits;

trait HasGoogleDriveLink
{
    /**
     * Extract the file ID from a Google Drive/Docs/Sheets/Slides URL so it
     * can be turned into an embeddable preview link. Only works if the file
     * is shared as "Anyone with the link".
     */
    protected function extractDriveFileId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('#/d/([a-zA-Z0-9_-]{10,})#', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/[?&]id=([a-zA-Z0-9_-]{10,})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

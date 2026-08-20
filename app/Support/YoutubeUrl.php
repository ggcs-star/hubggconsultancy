<?php

namespace App\Support;

class YoutubeUrl
{
    /**
     * Extracts the 11-char video ID from a plain YouTube URL (watch?v=, youtu.be/,
     * embed/, or /shorts/) or a pasted <iframe> embed snippet. Returns null for
     * anything else, including playlist links (see isPlaylist()).
     */
    public static function videoId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function isPlaylist(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        return str_contains($url, 'list=');
    }

    /**
     * True if the URL is a link this app can embed and track (i.e. we can pull a
     * video ID out of it and it isn't a playlist).
     */
    public static function isEmbeddable(?string $url): bool
    {
        return ! self::isPlaylist($url) && self::videoId($url) !== null;
    }
}

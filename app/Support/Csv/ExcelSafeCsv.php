<?php

namespace App\Support\Csv;

/**
 * Excel auto-detects plain numeric-looking CSV text (e.g. a phone number like "+917898678950")
 * and mangles it on open — dropping the leading "+" and sometimes more digits — even though the
 * CSV bytes on disk are correct. Wrapping the value as an ="..." formula cell forces Excel to
 * treat it as literal text instead, so the full value displays correctly.
 */
final class ExcelSafeCsv
{
    public static function guard(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return '="' . str_replace('"', '""', $value) . '"';
    }

    /** Reverses guard(), so a previously exported file can be re-imported without the wrapper leaking into stored data. */
    public static function unwrap(string $value): string
    {
        if (preg_match('/^="(.*)"$/s', $value, $matches)) {
            return str_replace('""', '"', $matches[1]);
        }

        return $value;
    }
}

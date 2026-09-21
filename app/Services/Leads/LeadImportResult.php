<?php

namespace App\Services\Leads;

use App\Models\Lead;

final class LeadImportResult
{
    /** @param  Lead[]  $createdLeads */
    public function __construct(
        public readonly int $imported = 0,
        public readonly int $skipped = 0,
        public readonly int $errors = 0,
        public readonly array $createdLeads = [],
    ) {
    }

    public function summary(): string
    {
        $summary = "{$this->imported} lead(s) imported.";

        if ($this->skipped > 0) {
            $summary .= " {$this->skipped} skipped as duplicates.";
        }

        if ($this->errors > 0) {
            $summary .= " {$this->errors} row(s) had errors and were skipped.";
        }

        return $summary;
    }
}

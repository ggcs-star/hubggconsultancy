<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lead Freeze Threshold
    |--------------------------------------------------------------------------
    |
    | A lead freezes for its assigned salesperson once this many days pass
    | with no status change. Frozen leads become read-only until an admin
    | approves the salesperson's unfreeze request.
    |
    */

    'freeze_after_days' => env('LEAD_FREEZE_AFTER_DAYS', 2),

    /*
    |--------------------------------------------------------------------------
    | Import Notification Emails
    |--------------------------------------------------------------------------
    |
    | Sent whenever a CSV import or the Drive auto-sync adds new leads —
    | a summary to every address below, plus a per-lead email to whichever
    | salesperson each new lead was assigned to (if any).
    |
    */

    'notify_emails' => array_filter(array_map(
        'trim',
        explode(',', (string) env('LEAD_IMPORT_NOTIFY_EMAILS', ''))
    )),

];

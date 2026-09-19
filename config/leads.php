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

];

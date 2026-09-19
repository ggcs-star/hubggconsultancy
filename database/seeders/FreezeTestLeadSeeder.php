<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Creates one lead assigned to Anuj Singh with its freeze clock backdated
 * past config('leads.freeze_after_days'), so the frozen UI and the
 * request-unfreeze flow can be tested by logging in as that account
 * without waiting for a real lead to go stale.
 *
 * Safe to re-run — keyed on a fixed name+phone pair via firstOrCreate.
 */
class FreezeTestLeadSeeder extends Seeder
{
    public function run(): void
    {
        $anuj = User::where('email', 'anuj63321@gmail.com')
            ->orWhere(function ($query) {
                $query->where('role', 'user')->where('name', 'like', 'Anuj%');
            })
            ->first();

        if (! $anuj) {
            $this->command?->warn('FreezeTestLeadSeeder: no user matching "Anuj Singh" found — skipped.');

            return;
        }

        $lead = Lead::firstOrCreate(
            ['name' => 'Freeze Test Lead', 'phone' => '9999900000'],
            [
                'company' => 'Test Freeze Co',
                'email' => 'freezetest@example.com',
                'product' => 'UPOS',
                'source' => 'Manual Test',
                'priority' => 'medium',
                'status' => 'new',
                'assigned_to' => $anuj->id,
                'created_by' => $anuj->id,
            ]
        );

        // Bypass Eloquent events so the freeze-clock backdate isn't immediately
        // overwritten by Lead::booted()'s "status changed -> reset the clock" hook.
        DB::table('leads')->where('id', $lead->id)->update([
            'status_changed_at' => now()->subDays(config('leads.freeze_after_days') + 1),
        ]);

        $this->command?->info("FreezeTestLeadSeeder: lead #{$lead->id} assigned to {$anuj->name} is frozen and ready to test.");
    }
}

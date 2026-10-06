<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Widen the status enum to add "Active Lead" and "Engaged Lead" — MySQL
        // requires a raw statement to redefine an enum's allowed values.
        DB::statement("ALTER TABLE leads MODIFY status ENUM(
            'new', 'contacted', 'active_lead', 'engaged_lead', 'interested', 'qualified',
            'proposal', 'negotiation', 'won', 'lost', 'not_interested', 'invalid', 'follow_up_later'
        ) NOT NULL DEFAULT 'new'");
    }

    public function down(): void
    {
        DB::table('leads')->whereIn('status', ['active_lead', 'engaged_lead'])->update(['status' => 'contacted']);

        DB::statement("ALTER TABLE leads MODIFY status ENUM(
            'new', 'contacted', 'interested', 'qualified', 'proposal', 'negotiation',
            'won', 'lost', 'not_interested', 'invalid', 'follow_up_later'
        ) NOT NULL DEFAULT 'new'");
    }
};

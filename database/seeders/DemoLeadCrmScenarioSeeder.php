<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Contest;
use App\Models\ContestPointRule;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo data for the Leads/CRM ↔ Campaign ↔ Contest pipeline: one campaign,
 * one CRM-driven contest (points awarded automatically as a lead moves
 * through its pipeline stages), and a spread of leads — assigned across
 * Rahul/Amit/Priya (seeded by DemoSalesScenarioSeeder) plus a couple of
 * unassigned ones and a duplicate-phone pair — walked through their stages
 * one at a time so the CRM auto-award hook actually fires realistically,
 * the same way it would from real usage.
 *
 * Run DemoSalesScenarioSeeder first. Safe to re-run: every write is keyed
 * on a natural unique field, and re-applying the same status to a lead is a
 * no-op (Eloquent only fires the update event when something actually changed),
 * so points are never double-awarded.
 */
class DemoLeadCrmScenarioSeeder extends Seeder
{
    /** Full forward pipeline, in order — used to walk a lead up to its target stage one step at a time. */
    private const STAGE_ORDER = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won'];

    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $rahul = User::where('email', 'rahul.sharma@demo.ggconsultancy.test')->first();
        $amit = User::where('email', 'amit.verma@demo.ggconsultancy.test')->first();
        $priya = User::where('email', 'priya.singh@demo.ggconsultancy.test')->first();

        if (! $rahul || ! $amit || ! $priya) {
            $this->command?->warn('Run DemoSalesScenarioSeeder first — Rahul/Amit/Priya not found.');

            return;
        }

        $campaign = Campaign::firstOrCreate(
            ['name' => 'GG Prime August Campaign'],
            [
                'description' => 'Lead generation push for the GG Prime UPOS launch.',
                'starts_at' => now()->startOfMonth(),
                'ends_at' => now()->startOfMonth()->addDays(28),
                'is_active' => true,
                'created_by' => $admin?->id,
            ]
        );

        $contest = Contest::firstOrCreate(
            ['name' => 'GG Prime Lead Generation Challenge'],
            [
                'description' => 'Earn points by moving your leads through the pipeline — from qualifying interest all the way to closing the sale.',
                'target_type' => 'new_customers',
                'target' => '150 pts',
                'target_value' => 150,
                'participation_type' => 'individual',
                'participant_mode' => 'open',
                'achievement_source' => 'crm',
                'starts_at' => now()->startOfMonth(),
                'ends_at' => now()->startOfMonth()->addDays(28),
                'reward' => '10,000 bonus',
                'reward_type' => 'bonus',
                'reward_second' => '5,000 bonus',
                'reward_third' => '2,500 bonus',
                'is_active' => true,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ]
        );

        // Make sure it's actually CRM-driven even if it already existed from a prior partial run.
        if ($contest->achievement_source !== 'crm') {
            $contest->update(['achievement_source' => 'crm', 'target_value' => 150]);
        }

        foreach (['qualified' => 10, 'proposal' => 20, 'negotiation' => 30, 'won' => 100] as $status => $points) {
            ContestPointRule::updateOrCreate(
                ['contest_id' => $contest->id, 'lead_status' => $status],
                ['points' => $points]
            );
        }

        $contest->participants()->syncWithoutDetaching([$rahul->id, $amit->id, $priya->id]);

        // Rahul — ends up leading the board (160 + 10 + 30 = 200 pts).
        $this->seedLead('Vikram Enterprises', '9800000001', 'Website', 'UPOS', 20000, $rahul, $campaign, $admin, 'won');
        $this->seedLead('Deepak Furnishings', '9800000002', 'Referral', 'UPOS', 12000, $rahul, $campaign, $admin, 'qualified');
        $this->seedLead('Suresh Traders', '9800000003', 'Instagram', 'UPOS Mini', 8000, $rahul, null, $admin, 'proposal');

        // Amit — mid-pack (60 + 10 = 70 pts).
        $this->seedLead('Meera Textiles', '9800000004', 'Facebook', 'UPOS', 15000, $amit, $campaign, $admin, 'negotiation');
        $this->seedLead('Anil Hardware', '9800000005', 'Cold Call / Direct', 'UPOS Mini', 6000, $amit, null, $admin, 'qualified');

        // Priya — lowest of the three (30 + 0 = 30 pts).
        $this->seedLead('Kavita Boutique', '9800000006', 'WhatsApp', 'UPOS', 10000, $priya, $campaign, $admin, 'proposal');
        $this->seedLead('Ramesh Traders', '9800000007', 'Event / Exhibition', 'UPOS Mini', 5000, $priya, null, $admin, 'contacted');

        // Unassigned — good candidates for "Auto-Assign Unassigned" and the New Leads dashboard count.
        $this->seedLead('Sanjay Enterprises', '9800000008', 'Website', 'UPOS', 18000, null, $campaign, $admin, 'new');
        $this->seedLead('Global Mart', '9800000009', 'LinkedIn', 'UPOS', 25000, null, null, $admin, 'new');

        // A duplicate-phone pair — demonstrates the "possible duplicate" warning on the lead detail page.
        $this->seedLead('Test Duplicate A', '9800000099', 'Import', 'UPOS', null, null, null, $admin, 'new');
        $this->seedLead('Test Duplicate B', '9800000099', 'Import', 'UPOS', null, null, null, $admin, 'new');
    }

    private function seedLead(
        string $name,
        string $phone,
        string $source,
        string $product,
        ?float $expectedValue,
        ?User $assignee,
        ?Campaign $campaign,
        ?User $admin,
        string $finalStatus,
    ): Lead {
        $lead = Lead::firstOrCreate(
            ['name' => $name, 'phone' => $phone],
            [
                'source' => $source,
                'product' => $product,
                'expected_value' => $expectedValue,
                'priority' => 'medium',
                'status' => 'new',
                'assigned_to' => $assignee?->id,
                'campaign_id' => $campaign?->id,
                'created_by' => $admin?->id,
            ]
        );

        $targetIndex = array_search($finalStatus, self::STAGE_ORDER, true);
        $currentIndex = array_search($lead->status, self::STAGE_ORDER, true);

        // Walk it forward one stage at a time so the CRM point-award hook fires
        // at each transition, exactly like it would from real admin/user usage.
        for ($i = max($currentIndex, 0) + 1; $i <= $targetIndex; $i++) {
            $lead->update(['status' => self::STAGE_ORDER[$i]]);
        }

        return $lead;
    }
}

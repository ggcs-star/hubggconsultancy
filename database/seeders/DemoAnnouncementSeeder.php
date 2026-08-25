<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A few sample announcements so the dashboard's "Latest Announcements"
 * widget has real content. Safe to re-run — keyed on title via firstOrCreate.
 */
class DemoAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        Announcement::firstOrCreate(
            ['title' => '100% First-Year Incentive now live for GG Prime Partners!'],
            ['body' => null, 'published_at' => now()->subDays(5), 'is_active' => true, 'created_by' => $admin?->id]
        );

        Announcement::firstOrCreate(
            ['title' => 'New Training: Objection Handling Mastery — Enroll Now'],
            ['body' => null, 'published_at' => now()->subDays(6), 'is_active' => true, 'created_by' => $admin?->id]
        );

        Announcement::firstOrCreate(
            ['title' => 'GG Prime Mega Contest — Win Big Rewards!'],
            ['body' => 'Join the contest and climb the leaderboard before it ends.', 'published_at' => now()->subDays(7), 'is_active' => true, 'created_by' => $admin?->id]
        );
    }
}

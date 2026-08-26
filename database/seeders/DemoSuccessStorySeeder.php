<?php

namespace Database\Seeders;

use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A few sample success stories so the Success Stories page has real content
 * to preview. Safe to re-run — keyed on name via firstOrCreate.
 */
class DemoSuccessStorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        SuccessStory::firstOrCreate(
            ['name' => 'Priya Patel'],
            [
                'designation' => 'Sales Executive',
                'headline' => 'From New Joiner to Top Performer',
                'testimonial' => 'The product training and objection-handling sessions helped me become much more confident while talking to customers.',
                'metrics' => [
                    ['label' => 'Sales Conversion', 'before' => '12%', 'after' => '27%'],
                    ['label' => 'Product Knowledge', 'before' => '58%', 'after' => '91%'],
                    ['label' => 'Certifications', 'before' => '0', 'after' => '5'],
                ],
                'business_impact' => 'Priya now consistently exceeds her monthly targets and mentors two new joiners on objection handling.',
                'is_active' => true,
                'created_by' => $admin?->id,
            ]
        );

        SuccessStory::firstOrCreate(
            ['name' => 'Rahul Sharma'],
            [
                'designation' => 'Sales Executive',
                'headline' => 'Turning Cold Calls Into Closed Deals',
                'testimonial' => 'Before the Advanced Sales Certification, I struggled to get past the first call. Now I know exactly how to structure a pitch.',
                'metrics' => [
                    ['label' => 'Calls to Conversion', 'before' => '1 in 20', 'after' => '1 in 6'],
                    ['label' => 'Avg. Deal Size', 'before' => '₹8,000', 'after' => '₹15,500'],
                ],
                'business_impact' => 'Rahul closed his biggest deal to date within a month of completing the certification.',
                'is_active' => true,
                'created_by' => $admin?->id,
            ]
        );

        SuccessStory::firstOrCreate(
            ['name' => 'Amit Shah'],
            [
                'designation' => 'Team Lead',
                'headline' => 'Building a Consistent Learning Habit',
                'testimonial' => 'Setting aside 20 minutes a day for the training resources completely changed how prepared I feel on client calls.',
                'metrics' => [
                    ['label' => 'Product Knowledge Score', 'before' => '64%', 'after' => '89%'],
                    ['label' => 'Client Satisfaction Rating', 'before' => '3.8 / 5', 'after' => '4.7 / 5'],
                ],
                'business_impact' => 'Amit now runs a weekly 15-minute knowledge-sharing huddle with his team based on what he learned.',
                'is_active' => true,
                'created_by' => $admin?->id,
            ]
        );
    }
}

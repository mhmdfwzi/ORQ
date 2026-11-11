<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure storage directory exists
        $storagePath = storage_path('app/public/team-images');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // 2. Define image mappings (Source in public/images -> Destination in storage)
        $images = [
            'team-ceo.png' => 'team-images/team-ceo.png',
            'team-director.png' => 'team-images/team-director.png',
            'team-manager.png' => 'team-images/team-manager.png',
        ];

        // 3. Copy files if valid
        foreach ($images as $sourceName => $destRelPath) {
            $sourceCtx = public_path('images/' . $sourceName);
            $destCtx = storage_path('app/public/' . $destRelPath);
            
            if (file_exists($sourceCtx)) {
                copy($sourceCtx, $destCtx);
            }
        }

        \App\Models\TeamMember::truncate();

        $members = [
            [
                'name' => 'Dr. Ahmed Mahmoud',
                'position' => 'Chief Executive Officer',
                'image' => 'team-images/team-ceo.png',
                'bio' => 'Dr. Ahmed has over 20 years of experience in the pharmaceutical industry.',
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'linkedin' => 'https://linkedin.com',
                    'twitter' => 'https://twitter.com',
                ],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Sarah Ali',
                'position' => 'Pharmacy Director',
                'image' => 'team-images/team-director.png',
                'bio' => 'Dr. Sarah leads our pharmacy operations with a focus on patient care and safety.',
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'linkedin' => 'https://linkedin.com',
                ],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Michael Hassan',
                'position' => 'Customer Service Manager',
                'image' => 'team-images/team-manager.png',
                'bio' => 'Michael ensures that every customer receives the best possible support and service.',
                'social_links' => [
                    'facebook' => 'https://facebook.com',
                    'twitter' => 'https://twitter.com',
                ],
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($members as $member) {
            \App\Models\TeamMember::create($member);
        }
    }
}

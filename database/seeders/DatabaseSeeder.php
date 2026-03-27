<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Order matters — seeders with foreign-key dependencies run after their parents.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,          // Users must exist first (campaigns, segments reference users)
            ContactSeeder::class,       // Contacts are standalone
            SegmentSeeder::class,       // Segments reference contacts via pivot
            CampaignSeeder::class,      // Campaigns reference segments and users
            EmailTemplateSeeder::class, // Email templates are standalone
            AbTestSeeder::class,        // A/B tests reference campaigns
        ]);
    }
}

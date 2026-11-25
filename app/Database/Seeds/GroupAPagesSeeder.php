<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds every "Group A" CMS page in one go (each seeder is idempotent):
 * Corporate Team, Selling a Home, Testimonials, CB Training Program, FAQ, Privacy Policy,
 * Terms & Conditions, Disclaimer, Refund Policy and Site Map.
 *
 *   php spark db:seed GroupAPagesSeeder
 */
class GroupAPagesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([CorporateTeamPageSeeder::class, SellingAHomePageSeeder::class, TestimonialsPageSeeder::class,
            TrainingPageSeeder::class, FaqPageSeeder::class, LegalPagesSeeder::class, SiteMapPageSeeder::class] as $seeder) {
            $this->call($seeder);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SauceSeeder::class,
            SpiceLevelSeeder::class,
            ToppingSeeder::class,
            ProductSeeder::class,
            ComboSeeder::class,
            HeroSeeder::class,
            BenefitSeeder::class,
            TestimonialSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeederRisk extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CatalogueRiskCategoriesSeeder::class,
            RiskInterpretationsSeeder::class,
            RiskAttributesSeeder::class,
            RiskHelpSeeder::class, 
        ]);
    }
}

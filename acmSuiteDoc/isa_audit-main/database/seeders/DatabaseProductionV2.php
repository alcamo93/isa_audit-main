<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseProductionV2 extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            EvaluationTypeSeeder::class,
            CatalogueStatusTableSeeder::class,
            CatalogueConditionsSeeder::class,
            CatalogueRiskCategoryAttributeSeeder::class,
            CatalogueMattersSeeder::class,
            CatalogueCategoriesSeeder::class,
        ]);
    }
}
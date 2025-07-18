<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseProductionStep2 extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CatalogueModulesSeeder::class,
            CatalogueSubmodulesSeeder::class,
            CatalogueRequirementTypesSeeder::class,
            DatabaseSeederRisk::class,
            ScopeSeeder::class,
            CatalogueCategoriesSeeder::class,
            CatalogueSorucesSeeder::class,
            CatalogueEvidencesSeeder::class,
            AnswerValuesSeeed::class
        ]);
    }
}
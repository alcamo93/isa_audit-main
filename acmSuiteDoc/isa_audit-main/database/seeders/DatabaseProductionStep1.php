<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseProductionStep1 extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CatalogueQuestionTypesSeeder::class,
            CatalogueApplicationTypesSeeder::class
        ]);
    }
}
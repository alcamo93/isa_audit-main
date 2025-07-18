<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueCountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_countries')->insert(['country' => 'México', 'country_code' => 'MEX',]);
    }
}

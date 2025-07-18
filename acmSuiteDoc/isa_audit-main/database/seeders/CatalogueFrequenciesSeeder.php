<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueFrequenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_frequencies')->insert(['frequency' => 'Una vez']);
        \DB::table('c_frequencies')->insert(['frequency' => 'Continua']);
        \DB::table('c_frequencies')->insert(['frequency' => 'Anual']);
    }
}
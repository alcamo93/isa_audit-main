<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('c_conditions')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        \DB::table('c_conditions')->insert(['condition' => 'Crítica']);
        \DB::table('c_conditions')->insert(['condition' => 'Operativa']);
        \DB::table('c_conditions')->insert(['condition' => 'Recomendacion']);
    }
}
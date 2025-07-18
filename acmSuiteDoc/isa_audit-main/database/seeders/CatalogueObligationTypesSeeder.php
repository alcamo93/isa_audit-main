<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueObligationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_obligation_types')->insert(['obligation_type' => 'Obligación']);
        \DB::table('c_obligation_types')->insert(['obligation_type' => 'Permiso']);
    }
}

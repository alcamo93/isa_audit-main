<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueApplicationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_application_types')->insert(['application_type' => 'Federal', 'group' => 1]);
        \DB::table('c_application_types')->insert(['application_type' => 'Estatal', 'group' => 1]);
        \DB::table('c_application_types')->insert(['application_type' => 'No aplica', 'group' => 2]);
        \DB::table('c_application_types')->insert(['application_type' => 'Local', 'group' => 1]);
        \DB::table('c_application_types')->insert(['application_type' => 'Corporativo', 'group' => 3]);
        \DB::table('c_application_types')->insert(['application_type' => 'Condicionante', 'group' => 3]);
        \DB::table('c_application_types')->insert(['application_type' => 'Acta de Inspección', 'group' => 3]);
        \DB::table('c_application_types')->insert(['application_type' => 'Específico', 'group' => 2]);
    }
}
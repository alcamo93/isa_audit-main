<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogueSorucesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('c_sources')->insert(['source' => 'Ninguno']);
        DB::table('c_sources')->insert(['source' => 'Plan de acción']);
        DB::table('c_sources')->insert(['source' => 'Obligaciones']);
    }
}

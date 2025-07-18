<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CataloguePeriodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_periods')->insert([
            'period' => '24 horas',
            'lastDay' => 1,
            'lastMonth' => 0,
            'lastYear' => 0,
            'lastRealDay' => 2,
            'lastRealMonth' => 0,
            'lastRealYear' => 0
        ]);
        \DB::table('c_periods')->insert([
            'period' => '15 días',
            'lastDay' => 15,
            'lastMonth' => 0,
            'lastYear' => 0,
            'lastRealDay' => 20,
            'lastRealMonth' => 0,
            'lastRealYear' => 0
        ]);
        \DB::table('c_periods')->insert([
            'period' => '1 mes',
            'lastDay' => 0,
            'lastMonth' => 1,
            'lastYear' => 0,
            'lastRealDay' => 10,
            'lastRealMonth' => 1,
            'lastRealYear' => 0
        ]);
        \DB::table('c_periods')->insert([
            'period' => '2 años',
            'lastDay' => 0,
            'lastMonth' => 0,
            'lastYear' => 2,
            'lastRealDay' => 0,
            'lastRealMonth' => 1,
            'lastRealYear' => 2
        ]);
    }
}
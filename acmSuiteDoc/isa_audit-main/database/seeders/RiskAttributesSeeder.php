<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RiskAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_risk_attributes')->insert([
            'risk_attribute' => 'Probabilidad',
        ]);
        \DB::table('c_risk_attributes')->insert([
            'risk_attribute' => 'Exposición',
        ]);
        \DB::table('c_risk_attributes')->insert([
            'risk_attribute' => 'Consecuencia',
        ]);
    }
}
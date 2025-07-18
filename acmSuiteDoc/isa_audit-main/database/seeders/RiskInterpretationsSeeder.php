<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RiskInterpretationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Bajo',
            'interpretation_min' => 0, 
            'interpretation_max' => 50,
            'id_risk_category' => 1,
        ]);
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Medio',
            'interpretation_min' => 51, 
            'interpretation_max' => 100,
            'id_risk_category' => 1,
        ]);
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Alto',
            'interpretation_min' => 101, 
            'interpretation_max' => 0,
            'id_risk_category' => 1,
        ]);

        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Bajo',
            'interpretation_min' => 0, 
            'interpretation_max' => 50,
            'id_risk_category' => 2,
        ]);
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Medio',
            'interpretation_min' => 51, 
            'interpretation_max' => 100,
            'id_risk_category' => 2,
        ]);
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Alto',
            'interpretation_min' => 101, 
            'interpretation_max' => 0,
            'id_risk_category' => 2,
        ]);

        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Bajo',
            'interpretation_min' => 0, 
            'interpretation_max' => 50,
            'id_risk_category' => 3,
        ]);
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Medio',
            'interpretation_min' => 51, 
            'interpretation_max' => 100,
            'id_risk_category' => 3,
        ]);
        \DB::table('t_risk_interpretations')->insert([
            'interpretation' => 'Alto',
            'interpretation_min' => 101, 
            'interpretation_max' => 0,
            'id_risk_category' => 3,
        ]);
    }
}
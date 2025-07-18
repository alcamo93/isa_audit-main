<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueRiskCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_risk_categories')->insert([
            'id_risk_category' => 1,
            'risk_category' => 'Riesgo Ambiental', 
            'id_status' => 1
        ]);
        \DB::table('c_risk_categories')->insert([
            'id_risk_category' => 2, 
            'risk_category' =>  'Riesgo Empresarial', 
            'id_status' => 1
        ]);
        \DB::table('c_risk_categories')->insert([
            'id_risk_category' => 3, 
            'risk_category' =>  'Riesgo Salud', 
            'id_status' => 1
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueAspectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_aspects')->insert(['aspect' => 'Impacto Ambiental', 'id_matter' => 1, 'order' => 1 ]);
        \DB::table('c_aspects')->insert(['aspect' => 'Riesgo Ambiental', 'id_matter' => 1, 'order' => 2 ]);
        \DB::table('c_aspects')->insert(['aspect' => 'Uso de Suelo', 'id_matter' => 1, 'order' => 3]);
        \DB::table('c_aspects')->insert(['aspect' => 'Abastecimiento de Agua', 'id_matter' => 1, 'order' =>  4]);
        \DB::table('c_aspects')->insert(['aspect' => 'Descarga de Agua', 'id_matter' => 1, 'order' =>  5]);
        \DB::table('c_aspects')->insert(['aspect' => 'Residuos Peligrosos', 'id_matter' => 1, 'order' =>  6]);
        \DB::table('c_aspects')->insert(['aspect' => 'Residuos De Manejo Especial', 'id_matter' => 1, 'order' =>  7]);
        \DB::table('c_aspects')->insert(['aspect' => 'Recursos Naturales', 'id_matter' => 1, 'order' =>  8]);
        \DB::table('c_aspects')->insert(['aspect' => 'Emergencias Ambientales', 'id_matter' => 1, 'order' =>  9]);
        
        
        \DB::table('c_aspects')->insert(['aspect' => 'Salud Ocupacional', 'id_matter' => 2, 'order' =>  1]);
        \DB::table('c_aspects')->insert(['aspect' => 'Organizacional', 'id_matter' => 2, 'order' =>  2]);
    }
}
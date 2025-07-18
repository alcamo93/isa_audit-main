<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueLegalClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('c_legal_classification')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Ley']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Reglamento']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Norma']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Acuerdos']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Código']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Decreto']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Lineamiento']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Orden Jurídico']); 
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Aviso']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Convocatoria']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Plan']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Programa']);
        \DB::table('c_legal_classification')->insert(['legal_classification' => 'Recomendaciones']);
    }
}
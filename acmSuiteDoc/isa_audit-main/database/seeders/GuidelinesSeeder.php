<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GuidelinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_guidelines')->insert([
            'guideline' => 'Reglamento de la LGEEPA en Materia de Evaluación de Impacto Ambienta',
            'initials_guideline' => 'RLGEEPAMEIA',
            'id_legal_c' => 2,
            'id_application_type' => 1
        ]);
        \DB::table('t_guidelines')->insert([
            'guideline' => 'Ley General del Equilibrio Ecológico y de Protección al Ambiente',
            'initials_guideline' => 'LGEEPA',
            'id_legal_c' => 1,
            'id_application_type' => 1
        ]);
        \DB::table('t_guidelines')->insert([
            'guideline' => 'Ley Federal de procedimiento Administrativo',
            'initials_guideline' => 'LFPA',
            'id_legal_c' => 1,
            'id_application_type' => 1
        ]);
        \DB::table('t_guidelines')->insert([
            'guideline' => 'Ley Federal de Derechos',
            'initials_guideline' => 'LFD',
            'id_legal_c' => 1,
            'id_application_type' => 1
        ]);
    }
}
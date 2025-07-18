<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogueRequirementTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Identificación Federal',
            'group' => 0,
            'identification' => 1
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Identificación Estatal',
            'group' => 0,
            'identification' => 1
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Desbloqueo de requisitos',
            'group' => 3,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Requerimiento Estatal',
            'group' => 0,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Requerimiento Compuesto',
            'group' => 0,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Identificación Federal',
            'group' => 1,
            'identification' => 1
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Desbloqueo de requisitos Federal',
            'group' => 3,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Identificación Estatal',
            'group' => 2,
            'identification' => 1
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Desbloqueo de requisitos Estatal',
            'group' => 3,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Subequerimientos Estatales',
            'group' => 2,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Requerimientos Específicos',
            'group' => 0,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Requerimiento Local',
            'group' => 0,
            'identification' => 0
        ]);    
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Identificación Local',
            'group' => 0,
            'identification' => 1
        ]);   
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Subrequerimiento Local',
            'group' => 4,
            'identification' => 0
        ]);    
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Identificación Local',
            'group' => 4,
            'identification' => 1
        ]);   
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Desbloqueo de requisitos local',
            'group' => 3,
            'identification' => 0
        ]);
        \DB::table('c_requirement_types')->insert([
            'requirement_type' => 'Requerimiento Compuesto de Identificación',
            'group' => 0,
            'identification' => 1
        ]);
    }
}
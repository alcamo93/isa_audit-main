<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RequirementsLegalBasiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 1, 'id_legal_basis' => 1]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 1, 'id_legal_basis' => 8]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 1, 'id_legal_basis' => 9]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 1, 'id_legal_basis' => 10]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 1, 'id_legal_basis' => 4]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 1, 'id_legal_basis' => 11]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 2, 'id_legal_basis' => 12]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 2, 'id_legal_basis' => 13]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 3, 'id_legal_basis' => 2]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 4, 'id_legal_basis' => 2]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 5, 'id_legal_basis' => 22]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 5, 'id_legal_basis' => 23]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 6, 'id_legal_basis' => 5]); 
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 7, 'id_legal_basis' => 14]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 7, 'id_legal_basis' => 15]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 8, 'id_legal_basis' => 16]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 8, 'id_legal_basis' => 17]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 8, 'id_legal_basis' => 18]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 8, 'id_legal_basis' => 19]);
        \DB::table('r_requirements_legal_basies')->insert(['id_requirement' => 9, 'id_legal_basis' => 24]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubrequirementsLegalBasiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 1, 'id_legal_basis' => 5]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 2, 'id_legal_basis' => 5]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 3, 'id_legal_basis' => 5]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 4, 'id_legal_basis' => 5]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 5, 'id_legal_basis' => 18]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 6, 'id_legal_basis' => 20]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 6, 'id_legal_basis' => 6]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 7, 'id_legal_basis' => 7]);
        \DB::table('r_subrequirements_legal_basies')->insert(['id_subrequirement' => 8, 'id_legal_basis' => 7]);
    }
}
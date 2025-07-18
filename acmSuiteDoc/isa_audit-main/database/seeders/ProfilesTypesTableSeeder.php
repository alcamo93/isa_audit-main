<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_profile_types')->insert(['type'=> 'Global ISA Ambiental', 'owner'=> 1, 'profile_level' => 1]);// Administrador del Sistema
        \DB::table('t_profile_types')->insert(['type'=> 'Operativo ISA Ambiental', 'owner'=> 1, 'profile_level' => 2]);// Operativo del Sistema
        \DB::table('t_profile_types')->insert(['type'=> 'Corporativo', 'owner'=> 0, 'profile_level' => 3]);// Administrador de los Corporativos
        \DB::table('t_profile_types')->insert(['type'=> 'Coordinador', 'owner'=> 0, 'profile_level' => 4]);// Administrador de un solo Corporativo
        \DB::table('t_profile_types')->insert(['type'=> 'Operativo', 'owner'=> 0, 'profile_level' => 5]);// Tarea personales de cuenta
    }
}

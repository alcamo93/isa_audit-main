<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('c_scope')->insert([
            'id_scope' => 1,
            'scope' => 'Planta'
        ]);
        \DB::table('c_scope')->insert([
            'id_scope' => 2,
            'scope' => 'Área/Departamento'
        ]);
    }
}
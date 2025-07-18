<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActionRegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        \DB::table('t_action_registers')->insert([
            'id_contract' => 1,
            'id_corporate' => 2,
            'id_status' => 1,
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AplicabilityRegistersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_aplicability_registers')->insert([
            'id_corporate' => 2, 
            'id_status' => 5,
            'id_contract' => 1,
        ]);
    }
}

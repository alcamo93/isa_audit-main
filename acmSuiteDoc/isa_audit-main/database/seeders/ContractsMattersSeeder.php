<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContractsMattersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('r_contract_matters')->insert([
            'self_audit' => 0,
            'id_aplicability_register' => 1,
            'id_contract' => 1, 
            'id_matter' => 1,
            'id_status' => 5
        ]);
        // \DB::table('r_contract_matters')->insert([
        //     'self_audit' => 0,
        //     'id_aplicability_register' => 1,
        //     'id_contract' => 1, 
        //     'id_matter' => 2,
        //     'id_status' => 4
        // ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContractsAspectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('r_contract_aspects')->insert([
            'self_audit' => 0,
            'id_contract_matter' => 1,
            'id_contract' => 1, 
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 3,
            'id_state' => 19
        ]);
        // \DB::table('r_contract_aspects')->insert([
        //     'self_audit' => 0,
        //     'id_contract_matter' => 1,
        //     'id_contract' => 1, 
        //     'id_matter' => 1,
        //     'id_aspect' => 2,
        //     'id_status' => 4,
        //     'id_state' => 19
        // ]);
        // \DB::table('r_contract_aspects')->insert([
        //     'self_audit' => 0,
        //     'id_contract_matter' => 1,
        //     'id_contract' => 1, 
        //     'id_matter' => 1,
        //     'id_aspect' => 3,
        //     'id_status' => 4,
        //     'id_state' => 19
        // ]);

        // \DB::table('r_contract_aspects')->insert([
        //     'self_audit' => 0,
        //     'id_contract_matter' => 2,
        //     'id_contract' => 1, 
        //     'id_matter' => 2,
        //     'id_aspect' => 8,
        //     'id_status' => 4,
        //     'id_state' => 19
        // ]);
        // \DB::table('r_contract_aspects')->insert([
        //     'self_audit' => 0,
        //     'id_contract_matter' => 2,
        //     'id_contract' => 1, 
        //     'id_matter' => 2,
        //     'id_aspect' => 9,
        //     'id_status' => 4,
        //     'id_state' => 19
        // ]);
    }
}

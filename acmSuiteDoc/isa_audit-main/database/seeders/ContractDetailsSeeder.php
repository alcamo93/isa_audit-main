<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContractDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_contract_details')->insert([
            'license' => 'Auditoria Completa', 
            'usr_global' => 2,
            'usr_corporate' => 3,
            'usr_operative' => 5,
            'id_period' => 4,
            'id_contract' => 1,
            'id_user' => 1
        ]);
    }
}

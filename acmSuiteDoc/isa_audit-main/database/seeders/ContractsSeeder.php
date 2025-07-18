<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContractsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_contracts')->insert([
            'contract' => 'C-190320-SINNTEC', 
            'start_date' => '2020-04-01 00:00:00',
            'end_date' => '2022-04-01 23:59:59',
            'id_status' => 1,
            'id_customer' => 1,
            'id_corporate' => 1
        ]);
    }
}

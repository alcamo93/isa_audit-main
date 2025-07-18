<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuditRegistersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_audit_registers')->insert([
            'id_corporate' => 2, 
            'id_status' => 2,
            'id_contract' => 1
        ]);
    }
}

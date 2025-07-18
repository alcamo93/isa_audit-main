<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuditMattersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('r_audit_matters')->insert([
            'self_audit' => 0,
            'id_audit_register' => 1,
            'id_contract' => 1, 
            'id_matter' => 1,
            'id_status' => 7
        ]);
        \DB::table('r_audit_matters')->insert([
            'self_audit' => 0,
            'id_audit_register' => 1,
            'id_contract' => 1, 
            'id_matter' => 2,
            'id_status' => 7
        ]);
    }
}

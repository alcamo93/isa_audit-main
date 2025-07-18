<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuditAspectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('r_audit_aspects')->insert([
            'self_audit' => 0,
            'id_audit_matter' => 1,
            'id_contract' => 1, 
            'id_matter' => 1,
            'id_aspect' => 1,
            'id_status' => 7
        ]);
        \DB::table('r_audit_aspects')->insert([
            'self_audit' => 0,
            'id_audit_matter' => 1,
            'id_contract' => 1, 
            'id_matter' => 1,
            'id_aspect' => 2,
            'id_status' => 7
        ]);
        \DB::table('r_audit_aspects')->insert([
            'self_audit' => 0,
            'id_audit_matter' => 1,
            'id_contract' => 1, 
            'id_matter' => 1,
            'id_aspect' => 3,
            'id_status' => 7
        ]);

        \DB::table('r_audit_aspects')->insert([
            'self_audit' => 0,
            'id_audit_matter' => 2,
            'id_contract' => 1, 
            'id_matter' => 2,
            'id_aspect' => 8,
            'id_status' => 7
        ]);
        \DB::table('r_audit_aspects')->insert([
            'self_audit' => 0,
            'id_audit_matter' => 2,
            'id_contract' => 1, 
            'id_matter' => 2,
            'id_aspect' => 9,
            'id_status' => 7
        ]);
    }
}

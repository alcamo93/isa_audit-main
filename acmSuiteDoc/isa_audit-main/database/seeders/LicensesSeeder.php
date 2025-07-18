<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LicensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_licenses')->insert([
            'license' => 'Auditoria Completa', 
            'usr_global' => 2,
            'usr_corporate' => 3,
            'usr_operative' => 5,
            'id_period' => 4,
            'id_status' => 1
        ]);
    }
}

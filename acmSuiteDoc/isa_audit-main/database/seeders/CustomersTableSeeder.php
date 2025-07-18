<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_customers')->insert([
            'cust_tradename' => 'ISA Ambiental', 
            'cust_trademark' => 'ISA Ambiental S.A de C.V',
            'sm_logo' => 'sm_1.png',
            'lg_logo' => 'lg_1.png',
            'owner' => 1
        ]);
        // \DB::table('t_customers')->insert([
        //     'cust_tradename' => 'Sinntec', 
        //     'cust_trademark' => 'Sinntec Innovación Tecnológica S.A de C.V'
        // ]);
    }
}

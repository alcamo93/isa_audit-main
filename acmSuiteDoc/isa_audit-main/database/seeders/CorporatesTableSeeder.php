<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorporatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_corporates')->insert([
            'corp_tradename' => 'ISA Ambiental', 
            'corp_trademark' => 'ISA Ambiental S.A de C.V',
            'rfc' => 'ISA123456789',
            'id_customer' => 1,
            'id_status' => 1,
            'id_industry' => 1,
            'type'=> 1
        ]);
        // \DB::table('t_corporates')->insert([
        //     'corp_tradename' => 'Sinntec', 
        //     'corp_trademark' => 'Sinntec Innovación Tecnológica S.A de C.V',
        //     'rfc' => 'TIS1405148H8',
        //     'id_customer' => 2,
        //     'id_status' => 1,
        //     'id_industry' => 1,
        //     'type'=> 1
        // ]);
    }
}

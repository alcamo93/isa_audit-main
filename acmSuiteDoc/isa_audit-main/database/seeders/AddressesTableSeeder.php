<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('t_addresses')->insert([
            'id_corporate' => 1, 
            'street' => 'Lázaro Cárdenas, 3er Piso',
            'ext_num' => '2321',
            'zip' => '66260',
            'suburb' => 'Residencial San Agustin',
            'type' => 1,
            'id_country' => 1,
            'id_state' => 19,
            'id_city' => 966
        ]);
    }
}
